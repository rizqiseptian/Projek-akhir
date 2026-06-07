<?php

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin login redirects to setup page when no admins exist', function () {
    $this->get(route('admin.login'))
        ->assertRedirect(route('admin.setup'));
});

test('admin login shows the login page when at least one admin exists', function () {
    Employee::create([
        'name' => 'Admin Boss',
        'rfid_uid' => '9999999999',
        'is_active' => true,
        'is_admin' => true,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $this->get(route('admin.login'))
        ->assertStatus(200)
        ->assertViewIs('employee-login');
});

test('setup page is accessible when no admins exist', function () {
    $this->get(route('admin.setup'))
        ->assertStatus(200)
        ->assertViewIs('admin-setup');
});

test('setup page redirects to admin login when an admin already exists', function () {
    Employee::create([
        'name' => 'Admin Boss',
        'rfid_uid' => '9999999999',
        'is_active' => true,
        'is_admin' => true,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $this->get(route('admin.setup'))
        ->assertRedirect(route('admin.login'));
});

test('registerFirstAdmin creates admin and logs in when no admin exists', function () {
    $response = $this->postJson(route('admin.register'), [
        'name' => 'New Admin',
        'rfid_uid' => 'ADMINCARD001',
        'face_descriptor' => json_encode(array_fill(0, 128, 0.25)),
        'whatsapp_number' => '+6281234567890',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'redirect' => '/admin',
        ]);

    $this->assertDatabaseHas('employees', [
        'name' => 'New Admin',
        'rfid_uid' => 'ADMINCARD001',
        'is_admin' => true,
        'is_active' => true,
    ]);

    // The new admin should be authenticated
    $this->assertAuthenticated();
});

test('registerFirstAdmin is blocked when an admin already exists', function () {
    Employee::create([
        'name' => 'Existing Admin',
        'rfid_uid' => '9999999999',
        'whatsapp_number' => '+6281234567890',
        'is_active' => true,
        'is_admin' => true,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    $response = $this->postJson(route('admin.register'), [
        'name' => 'Sneaky Second Admin',
        'rfid_uid' => 'NEWCARD001',
        'face_descriptor' => json_encode(array_fill(0, 128, 0.25)),
        'whatsapp_number' => '+6281234567890',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
        ]);

    // Only the original admin should exist
    $this->assertDatabaseCount('employees', 1);
    $this->assertGuest();
});

test('registerFirstAdmin rejects invalid face descriptor', function () {
    $response = $this->postJson(route('admin.register'), [
        'name' => 'New Admin',
        'rfid_uid' => 'ADMINCARD001',
        'face_descriptor' => json_encode([0.1, 0.2]), // Too short
        'whatsapp_number' => '+6281234567890',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => false,
            'message' => 'Invalid face data captured. Please try again.',
        ]);

    $this->assertDatabaseCount('employees', 0);
});

test('registerFirstAdmin requires whatsapp_number for first admin setup', function () {
    $response = $this->postJson(route('admin.register'), [
        'name' => 'New Admin',
        'rfid_uid' => 'ADMINCARD001',
        'face_descriptor' => json_encode(array_fill(0, 128, 0.25)),
    ]);

    $response->assertStatus(422);
    $this->assertDatabaseCount('employees', 0);
});

test('registerFirstAdmin rejects duplicate rfid_uid', function () {
    Employee::create([
        'name' => 'Existing Employee',
        'rfid_uid' => 'DUPLICATE001',
        'whatsapp_number' => '+6281234567890',
        'is_active' => true,
        'is_admin' => false,
        'face_descriptor' => json_encode(array_fill(0, 128, 0.1)),
    ]);

    // No admin exists, so setup is unlocked, but RFID is taken
    $response = $this->postJson(route('admin.register'), [
        'name' => 'New Admin',
        'rfid_uid' => 'DUPLICATE001',
        'face_descriptor' => json_encode(array_fill(0, 128, 0.25)),
        'whatsapp_number' => '+6281234567890',
    ]);

    $response->assertStatus(422); // Laravel validation error
});
