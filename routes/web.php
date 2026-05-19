<?php

use App\Http\Controllers\EmployeeLoginController;
use Illuminate\Support\Facades\Route;

// Redirect root to the employee login page
Route::redirect('/', '/login');

// Employee login
Route::get('/login',  [EmployeeLoginController::class, 'showLoginPage'])->name('employee.login');
Route::post('/login', [EmployeeLoginController::class, 'verifyEmployee'])->name('employee.verify');
Route::post('/emergency-bypass', [EmployeeLoginController::class, 'emergencyBypass'])->name('employee.emergencyBypass');
Route::post('/logout', [EmployeeLoginController::class, 'logout'])->name('employee.logout');
