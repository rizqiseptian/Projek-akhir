<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if (!Employee::where('is_admin', true)->where('is_active', true)->exists()) {
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
     * Only works when there are no active admins in the system.
     */
    public function registerFirstAdmin(Request $request)
    {
        if (Employee::where('is_admin', true)->where('is_active', true)->exists()) {
            return response()->json(['success' => false, 'message' => 'An administrator already exists. Please log in instead.']);
        }

        $request->validate([
            'name'            => 'required|string|max:255',
            'rfid_uid'        => 'required|string|unique:employees,rfid_uid',
            'face_descriptor' => 'required|string',
        ]);

        $faceDescriptor = json_decode($request->input('face_descriptor'), true);
        if (!is_array($faceDescriptor) || count($faceDescriptor) < 128) {
            return response()->json(['success' => false, 'message' => 'Invalid face data captured. Please try again.']);
        }

        $admin = Employee::create([
            'name'            => $request->input('name'),
            'rfid_uid'        => $request->input('rfid_uid'),
            'face_descriptor' => $request->input('face_descriptor'),
            'is_active'       => true,
            'is_admin'        => true,
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

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Invalid RFID Card.']);
        }

        if ($redirectTo === '/admin' && !$employee->is_admin) {
            return response()->json(['success' => false, 'message' => 'Access denied. You do not have administrator privileges.']);
        }

        if (!$employee->face_descriptor) {
            return response()->json(['success' => false, 'message' => 'Facial data not found. Please register your face first.']);
        }

        $captured = json_decode($faceDescriptorJson, true);
        $stored   = json_decode($employee->face_descriptor, true);

        if (!is_array($captured) || !is_array($stored) || count($captured) !== count($stored)) {
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
     * Emergency bypass: PIN-only override.
     *
     * Validates the secret EMERGENCY_PIN from .env.
     * On success, logs in as the first active employee so the
     * admin dashboard becomes accessible without RFID / face scan.
     */
    public function emergencyBypass(Request $request)
    {
        $pin = $request->input('pin');
        $redirectTo = $request->input('redirect_to', '/cashier');

        if ($pin !== env('EMERGENCY_PIN', '1234')) {
            return response()->json(['success' => false, 'message' => 'Invalid Emergency PIN.']);
        }

        // Log in as any active employee/admin depending on redirect path
        $query = Employee::where('is_active', true);
        if ($redirectTo === '/admin') {
            $query->where('is_admin', true);
        }
        $employee = $query->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'No active ' . ($redirectTo === '/admin' ? 'administrator' : 'employee') . ' account found in the system.']);
        }

        Auth::guard('web')->login($employee);
        $request->session()->regenerate();
        return response()->json(['success' => true, 'redirect' => $redirectTo]);
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
