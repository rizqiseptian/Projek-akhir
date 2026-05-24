<?php

use App\Http\Controllers\EmployeeLoginController;
use Illuminate\Support\Facades\Route;

// Redirect root to the employee login page
Route::redirect('/', '/login');

// Employee login
Route::get('/login',  [EmployeeLoginController::class, 'showLoginPage'])->name('employee.login');
Route::get('/admin/login',  [EmployeeLoginController::class, 'showAdminLoginPage'])->name('admin.login');
Route::get('/admin/setup',  [EmployeeLoginController::class, 'showAdminSetupPage'])->name('admin.setup');
Route::post('/login', [EmployeeLoginController::class, 'verifyEmployee'])->name('employee.verify');
Route::post('/admin/setup', [EmployeeLoginController::class, 'registerFirstAdmin'])->name('admin.register');
Route::post('/emergency-bypass', [EmployeeLoginController::class, 'emergencyBypass'])->name('employee.emergencyBypass');
Route::post('/logout', [EmployeeLoginController::class, 'logout'])->name('employee.logout');

// Standalone Cashier Page (requires login)
Route::middleware(\App\Http\Middleware\AuthenticateFilamentEmployee::class)->group(function () {
    Route::get('/cashier', \App\Livewire\Cashier::class)->name('cashier');
});
