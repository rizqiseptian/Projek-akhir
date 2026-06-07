<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmployeeLoginController extends Controller
{
    /**
     * Show the employee login page.
     * Always log out any existing session — this is a kiosk,
     * every visitor must scan their RFID + face fresh.
     */
    public function showLoginPage(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return view('employee-login', ['title' => 'Cashier Access', 'redirectTo' => '/cashier']);
    }

    /**
     * Show the admin login page (RFID + Face).
     * If no admins are registered yet, redirect to the first-run setup page.
     */
    public function showAdminLoginPage(Request $request)
    {
        if (! Employee::where('is_admin', true)->where('is_active', true)->exists()) {
            return redirect()->route('admin.setup');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return view('employee-login', ['title' => 'Admin Dashboard Access', 'redirectTo' => '/admin']);
    }

    /**
     * Show the first-run admin setup page.
     * Only accessible when no active admin exists.
     */
    public function showAdminSetupPage(Request $request)
    {
        if (Employee::where('is_admin', true)->where('is_active', true)->exists()) {
            return redirect()->route('admin.login');
        }

        return view('admin-setup');
    }

    /**
     * Register the very first administrator (RFID + face).
     * Every first-login setup step is mandatory and must be completed
     * before the first administrator account can be created.
     */
    public function registerFirstAdmin(Request $request)
    {
        if (Employee::where('is_admin', true)->where('is_active', true)->exists()) {
            return response()->json(['success' => false, 'message' => 'An administrator already exists. Please log in instead.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'rfid_uid' => 'required|string|unique:employees,rfid_uid',
            'face_descriptor' => 'required|string',
            'whatsapp_number' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9]{8,20}$/'],
        ]);

        $faceDescriptor = json_decode($request->input('face_descriptor'), true);
        if (! is_array($faceDescriptor) || count($faceDescriptor) !== 128) {
            return response()->json(['success' => false, 'message' => 'Invalid face data captured. Please try again.']);
        }

        $whatsappNumber = preg_replace('/[^0-9+]/', '', $request->input('whatsapp_number'));

        $admin = Employee::create([
            'name' => $request->input('name'),
            'rfid_uid' => $request->input('rfid_uid'),
            'face_descriptor' => $request->input('face_descriptor'),
            'whatsapp_number' => $whatsappNumber,
            'is_active' => true,
            'is_admin' => true,
        ]);

        Auth::guard('web')->login($admin);
        $request->session()->regenerate();

        return response()->json(['success' => true, 'redirect' => '/admin']);
    }

    /**
     * Handle RFID scan and face verification via AJAX.
     */
    public function verifyEmployee(Request $request)
    {
        $rfid = $request->input('rfid_uid');
        $faceDescriptorJson = $request->input('face_descriptor');
        $redirectTo = $request->input('redirect_to', '/cashier');

        $employee = Employee::where('rfid_uid', $rfid)
            ->where('is_active', true)
            ->first();

        if (! $employee) {
            return response()->json(['success' => false, 'message' => 'Invalid RFID Card.']);
        }

        if ($redirectTo === '/admin' && ! $employee->is_admin) {
            return response()->json(['success' => false, 'message' => 'Access denied. You do not have administrator privileges.']);
        }

        if (! $employee->face_descriptor) {
            return response()->json(['success' => false, 'message' => 'Facial data not found. Please register your face first.']);
        }

        $captured = json_decode($faceDescriptorJson, true);
        $stored = json_decode($employee->face_descriptor, true);

        if (! is_array($captured) || ! is_array($stored) || count($captured) !== count($stored)) {
            return response()->json(['success' => false, 'message' => 'Invalid face data. Please try again.']);
        }

        // Euclidean distance
        $sum = 0;
        for ($i = 0; $i < count($captured); $i++) {
            $sum += pow($captured[$i] - $stored[$i], 2);
        }
        $distance = sqrt($sum);

        if ($distance <= 0.5) {
            Auth::guard('web')->login($employee);
            $request->session()->regenerate();

            return response()->json(['success' => true, 'redirect' => $redirectTo]);
        }

        return response()->json(['success' => false, 'message' => 'Face verification failed. Faces do not match.']);
    }

    /**
     * Emergency bypass: OTP via admin WhatsApp.
     *
     * Sends a one-time code to the first active administrator's
     * WhatsApp number, then allows bypass when the OTP is verified.
     */
    public function emergencyBypass(Request $request)
    {
        $redirectTo = $request->input('redirect_to', '/cashier');
        $admin = Employee::where('is_admin', true)->where('is_active', true)->first();

        if (! $admin) {
            return response()->json(['success' => false, 'message' => 'No active administrator account found in the system.']);
        }

        if ($request->boolean('request_otp')) {
            if (! $admin->whatsapp_number) {
                return response()->json(['success' => false, 'message' => 'Administrator WhatsApp number is not configured.']);
            }

            if (! $this->sendEmergencyOtpMessage($admin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to send OTP via WhatsApp bot. Please verify the administrator WhatsApp number and bot configuration.',
                ]);
            }

            $message = 'An OTP has been sent to the administrator WhatsApp number.';
            if (config('app.env') === 'local' && (! env('WHATSAPP_API_URL') || ! env('WHATSAPP_API_TOKEN'))) {
                $cacheKey = "emergency_otp_admin_{$admin->id}";
                $otp = Cache::get($cacheKey);
                $message .= " (Simulated: check storage/logs/laravel.log. OTP is {$otp})";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        $otp = $request->input('otp', $request->input('pin'));
        if (! $otp) {
            return response()->json(['success' => false, 'message' => 'Please request an OTP first.']);
        }

        $cacheKey = "emergency_otp_admin_{$admin->id}";
        $expectedOtp = Cache::get($cacheKey);

        if (! $expectedOtp || ! hash_equals((string) $expectedOtp, (string) $otp)) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP. Please request a new code.']);
        }

        Cache::forget($cacheKey);
        Auth::guard('web')->login($admin);
        $request->session()->regenerate();

        return response()->json(['success' => true, 'redirect' => $redirectTo]);
    }

    private function sendEmergencyOtpMessage(Employee $admin): bool
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = "emergency_otp_admin_{$admin->id}";
        Cache::put($cacheKey, $otp, now()->addMinutes(5));

        $message = "Your emergency login OTP is {$otp}. It expires in 5 minutes.";
        $whatsappNumber = $admin->whatsapp_number;
        $apiUrl = env('WHATSAPP_API_URL');
        $apiToken = env('WHATSAPP_API_TOKEN');

        if (! $apiUrl || ! $apiToken) {
            Log::info('Emergency OTP generated for administrator (simulated - config missing)', [
                'admin_id' => $admin->id,
                'whatsapp_number' => $whatsappNumber,
                'otp' => $otp,
            ]);
            if (config('app.env') === 'local' || config('app.env') === 'testing') {
                return true;
            }
            Log::error('Emergency WhatsApp OTP configuration missing', [
                'admin_id' => $admin->id,
                'whatsapp_number' => $whatsappNumber,
            ]);

            return false;
        }

        try {
            $payload = [
                'phone' => $whatsappNumber,
                'to' => $whatsappNumber,
                'message' => $message,
                'body' => $message,
            ];

            $response = Http::withToken($apiToken)->post($apiUrl, $payload);

            if (! $response->successful()) {
                Cache::forget($cacheKey);
                Log::error('Emergency WhatsApp OTP delivery failed', [
                    'admin_id' => $admin->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload,
                ]);

                return false;
            }

            Log::info('Emergency WhatsApp OTP sent', ['admin_id' => $admin->id, 'whatsapp_number' => $whatsappNumber]);

            return true;
        } catch (\Throwable $exception) {
            Cache::forget($cacheKey);
            Log::error('Emergency WhatsApp OTP send error', [
                'admin_id' => $admin->id,
                'exception' => $exception->getMessage(),
                'whatsapp_number' => $whatsappNumber,
            ]);

            return false;
        }
    }

    /**
     * Log out the employee.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
