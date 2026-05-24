<?php

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it fails to bypass with invalid pin', function () {
    $response = $this->postJson(route('employee.emergencyBypass'), [
        'pin' => 'invalid_pin',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
            'message' => 'Invalid Emergency PIN.',
        ]);
});

test('it fails to bypass when no active employee is found', function () {
    $response = $this->postJson(route('employee.emergencyBypass'), [
        'pin' => '1234',
        'redirect_to' => '/cashier',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
            'message' => 'No active employee account found in the system.',
        ]);
});

test('it fails to bypass to admin panel when no active administrator is found', function () {
    // Create an active employee but NOT an admin
    Employee::create([
        'name' => 'Cashier Guy',
        'rfid_uid' => '1111111111',
        'is_active' => true,
        'is_admin' => false,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $response = $this->postJson(route('employee.emergencyBypass'), [
        'pin' => '1234',
        'redirect_to' => '/admin',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
            'message' => 'No active administrator account found in the system.',
        ]);
});

test('it successfully bypasses with default redirect to cashier', function () {
    $employee = Employee::create([
        'name' => 'John Doe',
        'rfid_uid' => '1234567890',
        'is_active' => true,
        'is_admin' => false,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $response = $this->postJson(route('employee.emergencyBypass'), [
        'pin' => '1234',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'redirect' => '/cashier',
        ]);

    $this->assertAuthenticatedAs($employee);
});

test('it successfully bypasses to admin panel when an active administrator is found', function () {
    $admin = Employee::create([
        'name' => 'Admin Boss',
        'rfid_uid' => '9999999999',
        'is_active' => true,
        'is_admin' => true,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $response = $this->postJson(route('employee.emergencyBypass'), [
        'pin' => '1234',
        'redirect_to' => '/admin',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'redirect' => '/admin',
        ]);

    $this->assertAuthenticatedAs($admin);
});
