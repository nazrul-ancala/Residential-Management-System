<?php

use App\Http\Controllers\dashboard\manageDashboardController;
use App\Http\Controllers\maintenance\manageMaintenanceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing.homepage')->name('homepage');
Route::view('/login', 'auth.login')->name('login');

Route::get('/dashboard', [manageDashboardController::class, 'index'])->name('dashboard_utama');
Route::get('/maintenance', [manageMaintenanceController::class, 'index'])->name('manageMaintenance');
