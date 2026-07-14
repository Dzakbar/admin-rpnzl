<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController as ApiBookingController;
use App\Http\Controllers\Api\CompanyProfileController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/company-profile', [CompanyProfileController::class, 'show'])
    ->name('api.company-profile');

Route::post('/auth/register', [AuthController::class, 'register'])
    ->name('api.auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])
    ->name('api.auth.login');
Route::post('/auth/google', [AuthController::class, 'googleLogin'])
    ->name('api.auth.google');

Route::post('/bookings', [ApiBookingController::class, 'store'])
    ->name('api.bookings.store');

Route::get('/testimonials', [TestimonialController::class, 'index'])
    ->name('api.testimonials.index');
Route::post('/testimonials', [TestimonialController::class, 'store'])
    ->name('api.testimonials.store');
Route::post('/testimonials/home', [TestimonialController::class, 'storeHome'])
    ->name('api.testimonials.home.store');
Route::post('/testimonials/bookings/{booking}', [TestimonialController::class, 'storeBooking'])
    ->name('api.testimonials.bookings.store');

Route::get('/schedules/availability', [ScheduleController::class, 'availability'])
    ->name('schedules.availability');

Route::get('/schedules/slots', [ScheduleController::class, 'slots'])
    ->name('schedules.slots');

Route::get('/schedules/unavailable', [ScheduleController::class, 'unavailable'])
    ->name('schedules.unavailable');

Route::get('/user/bookings', [UserController::class, 'bookings'])
    ->name('api.user.bookings');
