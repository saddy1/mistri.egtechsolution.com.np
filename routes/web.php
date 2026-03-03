<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; // WEB auth controller (admin login/logout)
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminRequestController;

// Admin auth
Route::get('/', [AuthController::class, 'showAdminLogin'])->name('admin.login.form');
Route::post('/login/admin', [AuthController::class, 'adminLogin'])->name('admin.login');

Route::group(['middleware' => 'admin.auth'], function () {

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');

    // WOW dashboard page (requests listing)
    Route::get('/admin/requests', [AdminRequestController::class, 'index'])->name('admin.requests.index');

    // Actions
    Route::post('/admin/requests/{serviceRequest}/solve', [AdminRequestController::class, 'solve'])
        ->name('admin.requests.solve');

    Route::delete('/admin/requests/{serviceRequest}', [AdminRequestController::class, 'destroy'])
        ->name('admin.requests.destroy');


        Route::get('/admin/requests/{serviceRequest}', [AdminRequestController::class, 'show'])
    ->name('admin.requests.show');
    // Admin logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});