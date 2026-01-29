<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PaymentMethodController;

// Public Routes (Peserta)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/event/{id}/details', [HomeController::class, 'getEventDetails'])->name('event.details');
Route::post('/search-participant', [HomeController::class, 'searchParticipant'])->name('search.participant');
Route::post('/daftar', [HomeController::class, 'storeParticipant'])->name('register.participant');

// Admin Routes
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    // ROUTE untuk mendapatkan data peserta
    Route::get('/participant/{id}', [AdminController::class, 'getParticipant'])->name('participant.show');
    
    // CUSTOM ROUTES 
    Route::post('/peserta/{id}/update-status', [ParticipantController::class, 'update'])->name('peserta.update-status');
    Route::post('/peserta/{id}/send-email', [ParticipantController::class, 'sendEmail'])->name('peserta.send-email');
    Route::get('/peserta/{id}/test-brevo', [ParticipantController::class, 'testBrevoEmail'])->name('peserta.test-brevo');
    Route::get('/peserta/search-by-code', [ParticipantController::class, 'searchByTransactionCode'])->name('peserta.search-by-code');
    
    // Resource Routes
    Route::resource('event', EventController::class);
    Route::resource('peserta', ParticipantController::class)->except(['update']);
    Route::resource('payment', PaymentMethodController::class);
});

// Dashboard Route
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view("dashboard");
    })->name('dashboard');
});