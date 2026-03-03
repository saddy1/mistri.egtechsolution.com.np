<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserRequestController;
use App\Http\Controllers\Api\AuthController;



Route::get(
    '/me',
    [AuthController::class, 'me']
)->middleware('auth:sanctum');
Route::post('/user/request-service', [UserRequestController::class, 'store'])
    ->middleware('auth:sanctum');
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post(
    '/complete-profile',
    [AuthController::class, 'completeProfile']
)->middleware('auth:sanctum');

Route::post('/auth/google', [AuthController::class, 'googleLogin']);
