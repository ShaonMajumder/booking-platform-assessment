<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['throttle:public-api'])->group(function () {
    Route::get('services', [ServiceController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::get('bookings/{bookingId}', [BookingController::class, 'show']);
});

Route::prefix('admin')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:admin');

    Route::middleware(['auth:admin','throttle:admin-api'])->group(function () {
        Route::apiResource('services', AdminServiceController::class);
        Route::get('bookings', [AdminBookingController::class, 'index']);
        Route::get('bookings/{id}', [AdminBookingController::class, 'show']);
    });
});