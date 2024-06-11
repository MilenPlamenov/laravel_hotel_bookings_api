<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
Route::get('users', [AuthController::class, 'getUsers'])->name('get.users')->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('payments', PaymentController::class);
});