<?php

use App\Http\Controllers\Api\BookingController as ApiBookingController;
use App\Http\Controllers\Api\CompanyProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/company-profile', [CompanyProfileController::class, 'show'])
    ->name('api.company-profile');

Route::post('/bookings', [ApiBookingController::class, 'store'])
    ->name('api.bookings.store');

Route::get('/schedules/availability', [ScheduleController::class, 'availability'])
    ->name('schedules.availability');

Route::get('/schedules/slots', [ScheduleController::class, 'slots'])
    ->name('schedules.slots');

Route::get('/schedules/unavailable', [ScheduleController::class, 'unavailable'])
    ->name('schedules.unavailable');

Route::get('/user/bookings', [UserController::class, 'bookings'])
    ->name('api.user.bookings');

Route::get('/user/bookings', [UserController::class, 'bookings'])
    ->name('api.user.bookings');
