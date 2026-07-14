<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

// ─── Public routes ───────────────────────────────────────────────────
Route::redirect('/', '/login')->name('home');

// Auth
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// ─── User routes (harus login) ───────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/booking',         [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking',        [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/status',  [BookingController::class, 'index'])->name('booking.index');
});

// ─── Admin routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Booking management
    Route::get('/bookings',               [Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}',     [Admin\BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/status', [Admin\BookingController::class, 'updateStatus'])->name('bookings.status');

    // Testimonial management
    Route::get('/testimonials', [Admin\TestimonialController::class, 'index'])->name('testimonials.index');
    Route::patch('/testimonials/{testimonial}/status', [Admin\TestimonialController::class, 'updateStatus'])->name('testimonials.status');
    Route::patch('/testimonials/{testimonial}/featured', [Admin\TestimonialController::class, 'toggleFeatured'])->name('testimonials.featured');
    Route::delete('/testimonials/{testimonial}', [Admin\TestimonialController::class, 'destroy'])->name('testimonials.destroy');

    // Schedule management
    Route::get('/schedule',                         [Admin\ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/schedule',                        [Admin\ScheduleController::class, 'store'])->name('schedule.store');
    Route::put('/schedule/{schedule}',              [Admin\ScheduleController::class, 'update'])->name('schedule.update');
    Route::delete('/schedule/{schedule}',           [Admin\ScheduleController::class, 'destroy'])->name('schedule.destroy');
    Route::post('/schedule/block',                  [Admin\ScheduleController::class, 'store'])->name('schedule.block');
    Route::delete('/schedule/{schedule}/unblock',   [Admin\ScheduleController::class, 'unblock'])->name('schedule.unblock');

    // Invoice
    Route::get('/invoices',                             [Admin\InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/{booking}/generate',         [Admin\InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::patch('/invoices/{invoice}',                 [Admin\InvoiceController::class, 'update'])->name('invoices.update');
    Route::get('/invoices/{invoice}/download',          [Admin\InvoiceController::class, 'download'])->name('invoices.download');

    // CMS
    Route::prefix('cms')->name('cms.')->group(function () {
        // Packages
        Route::get('/packages',              [Admin\CMS\PackageController::class, 'index'])->name('packages.index');
        Route::post('/packages',             [Admin\CMS\PackageController::class, 'store'])->name('packages.store');
        Route::put('/packages/{package}',    [Admin\CMS\PackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [Admin\CMS\PackageController::class, 'destroy'])->name('packages.destroy');

        // Gallery
        Route::get('/gallery',            [Admin\CMS\GalleryController::class, 'index'])->name('gallery.index');
        Route::post('/gallery',           [Admin\CMS\GalleryController::class, 'store'])->name('gallery.store');
        Route::delete('/gallery/{gallery}',[Admin\CMS\GalleryController::class, 'destroy'])->name('gallery.destroy');
        Route::patch('/gallery/reorder',   [Admin\CMS\GalleryController::class, 'reorder'])->name('gallery.reorder');
    });

    // Users
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
});
