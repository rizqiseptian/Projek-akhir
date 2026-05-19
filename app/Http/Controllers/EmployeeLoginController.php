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

        return view('employee-login');
    }

    /**
     * Handle RFID scan and face verification via AJAX.
     */
    public function verifyEmployee(Request $request)
    {
        $rfid = $request->input('rfid_uid');
        $faceDescriptorJson = $request->input('face_descriptor');

        $employee = Employee::where('rfid_uid', $rfid)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Invalid RFID Card.']);
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
            Auth::login($employee);
            return response()->json(['success' => true, 'redirect' => '/admin']);
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

        if ($pin !== env('EMERGENCY_PIN', '1234')) {
            return response()->json(['success' => false, 'message' => 'Invalid Emergency PIN.']);
        }

        // Log in as any active employee — sufficient to pass the auth middleware
        $employee = Employee::where('is_active', true)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'No active employee account found in the system.']);
        }

        Auth::login($employee);
        return response()->json(['success' => true, 'redirect' => '/admin']);
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
