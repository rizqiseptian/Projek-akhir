<?php

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('verifyEmployee endpoint blocks non-admins when redirecting to admin panel', function () {
    $employee = Employee::create([
        'name' => 'Regular Cashier',
        'rfid_uid' => '1111111111',
        'is_active' => true,
        'is_admin' => false,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $response = $this->postJson(route('employee.verify'), [
        'rfid_uid' => '1111111111',
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
        'redirect_to' => '/admin',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
            'message' => 'Access denied. You do not have administrator privileges.',
        ]);

    $this->assertGuest();
});

test('verifyEmployee endpoint allows admins to log in to admin panel', function () {
    $admin = Employee::create([
        'name' => 'Admin Boss',
        'rfid_uid' => '9999999999',
        'is_active' => true,
        'is_admin' => true,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $response = $this->postJson(route('employee.verify'), [
        'rfid_uid' => '9999999999',
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
        'redirect_to' => '/admin',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'redirect' => '/admin',
        ]);

    $this->assertAuthenticatedAs($admin);
});

test('verifyEmployee endpoint allows both employees and admins to log in to cashier', function () {
    $employee = Employee::create([
        'name' => 'Regular Cashier',
        'rfid_uid' => '1111111111',
        'is_active' => true,
        'is_admin' => false,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $response = $this->postJson(route('employee.verify'), [
        'rfid_uid' => '1111111111',
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
        'redirect_to' => '/cashier',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'redirect' => '/cashier',
        ]);

    $this->assertAuthenticatedAs($employee);
});

test('admin panel routes reject regular employees but allow admins', function () {
    $employee = Employee::create([
        'name' => 'Regular Cashier',
        'rfid_uid' => '1111111111',
        'is_active' => true,
        'is_admin' => false,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $admin = Employee::create([
        'name' => 'Admin Boss',
        'rfid_uid' => '9999999999',
        'is_active' => true,
        'is_admin' => true,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    // Unauthenticated should redirect
    $this->get('/admin')->assertRedirect(route('employee.login'));

    // Authenticated as employee should fail with 403
    $this->actingAs($employee)
        ->get('/admin')
        ->assertStatus(403);

    // Authenticated as admin should pass
    $this->actingAs($admin)
        ->get('/admin')
        ->assertStatus(200);
});

test('cashier route allows both regular employees and admins', function () {
    $employee = Employee::create([
        'name' => 'Regular Cashier',
        'rfid_uid' => '1111111111',
        'is_active' => true,
        'is_admin' => false,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $admin = Employee::create([
        'name' => 'Admin Boss',
        'rfid_uid' => '9999999999',
        'is_active' => true,
        'is_admin' => true,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    // Unauthenticated should redirect
    $this->get('/cashier')->assertRedirect(route('employee.login'));

    // Authenticated as employee should pass (Livewire render will return 200 or render correctly)
    $this->actingAs($employee)
        ->get('/cashier')
        ->assertStatus(200);

    // Authenticated as admin should pass
    $this->actingAs($admin)
        ->get('/cashier')
        ->assertStatus(200);
});
