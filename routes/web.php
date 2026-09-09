<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\SpecialistController as AdminSpecialistController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CompletedMaintenanceRecordController;
use App\Http\Controllers\CompletedReminderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventAttendanceController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicGarageController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleDocumentController;
use App\Http\Controllers\VehicleMemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/garage/shared/{publicId}', [PublicGarageController::class, 'show'])->name('garage.public');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::middleware('verified')->group(function (): void {
        Route::get('/app', DashboardController::class)->name('dashboard');

        Route::resource('vehicles', VehicleController::class)->except(['edit']);
        Route::post('/vehicles/{vehicle}/members', [VehicleMemberController::class, 'store'])
            ->name('vehicles.members.store');
        Route::delete('/vehicles/{vehicle}/members/{member}', [VehicleMemberController::class, 'destroy'])
            ->name('vehicles.members.destroy');

        Route::post('/vehicles/{vehicle}/maintenance-records', [MaintenanceRecordController::class, 'store'])
            ->name('vehicles.maintenance-records.store');
        Route::delete('/vehicles/{vehicle}/maintenance-records/{maintenanceRecord}', [MaintenanceRecordController::class, 'destroy'])
            ->scopeBindings()
            ->name('vehicles.maintenance-records.destroy');
        Route::post('/vehicles/{vehicle}/maintenance-records/{maintenanceRecord}/completion', [CompletedMaintenanceRecordController::class, 'store'])
            ->scopeBindings()
            ->name('vehicles.maintenance-records.completion.store');

        Route::post('/vehicles/{vehicle}/reminders', [ReminderController::class, 'store'])
            ->name('vehicles.reminders.store');
        Route::delete('/vehicles/{vehicle}/reminders/{reminder}', [ReminderController::class, 'destroy'])
            ->scopeBindings()
            ->name('vehicles.reminders.destroy');
        Route::post('/vehicles/{vehicle}/reminders/{reminder}/completion', [CompletedReminderController::class, 'store'])
            ->scopeBindings()
            ->name('vehicles.reminders.completion.store');

        Route::post('/vehicles/{vehicle}/documents', [VehicleDocumentController::class, 'store'])
            ->name('vehicles.documents.store');
        Route::get('/vehicles/{vehicle}/documents/{vehicleDocument}', [VehicleDocumentController::class, 'show'])
            ->scopeBindings()
            ->name('vehicles.documents.show');
        Route::delete('/vehicles/{vehicle}/documents/{vehicleDocument}', [VehicleDocumentController::class, 'destroy'])
            ->scopeBindings()
            ->name('vehicles.documents.destroy');

        Route::resource('events', EventController::class)->only(['index', 'show']);
        Route::post('/events/{event}/attendance', [EventAttendanceController::class, 'store'])->name('events.attendance.store');
        Route::delete('/events/{event}/attendance', [EventAttendanceController::class, 'destroy'])->name('events.attendance.destroy');

        Route::resource('specialists', SpecialistController::class)->only(['index', 'show']);
        Route::resource('bookings', BookingController::class)->only(['index', 'store', 'destroy']);

        Route::get('/community', CommunityController::class)->name('community');
        Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
        Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

        Route::get('/settings', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/settings', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/notifications/read', [NotificationController::class, 'store'])->name('notifications.read');

        Route::prefix('admin')->name('admin.')->middleware('can:access-admin')->group(function (): void {
            Route::get('/', AdminDashboardController::class)->name('dashboard');
            Route::resource('events', AdminEventController::class)->except(['show']);
            Route::resource('specialists', AdminSpecialistController::class)->except(['show']);
            Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
            Route::patch('/bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
        });
    });
});
