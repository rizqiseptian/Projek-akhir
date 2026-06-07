<?php

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('it sends an emergency OTP to the admin WhatsApp number', function () {
    putenv('WHATSAPP_API_URL=http://localhost/whatsapp/send');
    putenv('WHATSAPP_API_TOKEN=test-token');
    $_ENV['WHATSAPP_API_URL'] = 'http://localhost/whatsapp/send';
    $_ENV['WHATSAPP_API_TOKEN'] = 'test-token';
    $_SERVER['WHATSAPP_API_URL'] = 'http://localhost/whatsapp/send';
    $_SERVER['WHATSAPP_API_TOKEN'] = 'test-token';

    Http::fake([
        'http://localhost/whatsapp/send' => Http::response(['status' => 'ok'], 200),
    ]);

    $admin = Employee::create([
        'name' => 'Admin Boss',
        'rfid_uid' => '9999999999',
        'whatsapp_number' => '+6281234567890',
        'is_active' => true,
        'is_admin' => true,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $response = $this->postJson(route('employee.emergencyBypass'), [
        'request_otp' => true,
        'redirect_to' => '/admin',
    ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    Http::assertSent(function ($request) use ($admin) {
        return $request->url() === 'http://localhost/whatsapp/send'
            && $request['phone'] === $admin->whatsapp_number
            && $request['to'] === $admin->whatsapp_number
            && str_contains($request['message'], 'Your emergency login OTP is');
    });

    $this->assertNotNull(Cache::get("emergency_otp_admin_{$admin->id}"));
});

test('it fails to request an emergency OTP when no active administrator exists', function () {
    $response = $this->postJson(route('employee.emergencyBypass'), [
        'request_otp' => true,
        'redirect_to' => '/admin',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
            'message' => 'No active administrator account found in the system.',
        ]);
});

test('it fails verification with invalid or expired OTP', function () {
    $admin = Employee::create([
        'name' => 'Admin Boss',
        'rfid_uid' => '9999999999',
        'whatsapp_number' => '+6281234567890',
        'is_active' => true,
        'is_admin' => true,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    Cache::put("emergency_otp_admin_{$admin->id}", '654321', now()->addMinutes(5));

    $response = $this->postJson(route('employee.emergencyBypass'), [
        'otp' => '000000',
        'redirect_to' => '/admin',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
            'message' => 'Invalid or expired OTP. Please request a new code.',
        ]);
});

test('it successfully bypasses to admin panel with a valid OTP', function () {
    $admin = Employee::create([
        'name' => 'Admin Boss',
        'rfid_uid' => '9999999999',
        'whatsapp_number' => '+6281234567890',
        'is_active' => true,
        'is_admin' => true,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    Cache::put("emergency_otp_admin_{$admin->id}", '123456', now()->addMinutes(5));

    $response = $this->postJson(route('employee.emergencyBypass'), [
        'otp' => '123456',
        'redirect_to' => '/admin',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'redirect' => '/admin',
        ]);

    $this->assertAuthenticatedAs($admin);
    $this->assertNull(Cache::get("emergency_otp_admin_{$admin->id}"));
});
