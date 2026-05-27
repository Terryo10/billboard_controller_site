<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Livewire\BookingWizard;
use Illuminate\Support\Facades\Route;

// --- Public routes ---
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/stations', [PublicController::class, 'stationsIndex'])->name('stations.index');
Route::get('/stations/{station}', [PublicController::class, 'stationShow'])->name('stations.show');

// Booking wizard (Livewire)
Route::get('/book', BookingWizard::class)->name('booking.create');

// Payment
Route::middleware(['auth'])->group(function () {
    Route::get('/booking/{booking}/pay', [PaymentController::class, 'show'])->name('booking.payment');
    Route::get('/booking/{booking}/pay/success', [PaymentController::class, 'success'])->name('booking.payment.success');
    Route::post('/booking/{booking}/ecocash', [PaymentController::class, 'ecocash'])->name('booking.ecocash');
    Route::get('/my-adverts', [PublicController::class, 'myAdverts'])->name('my-adverts');
    Route::patch('/booking/{booking}/cancel', [PublicController::class, 'cancelBooking'])->name('booking.cancel');
});

// Stripe webhook (no CSRF)
Route::post('/webhook/stripe', [PaymentController::class, 'handleWebhook'])
    ->name('webhook.stripe')
    ->withoutMiddleware(['web']);

// Breeze auth routes (dashboard redirect to my-adverts)
Route::get('/dashboard', fn () => redirect()->route('my-adverts'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
