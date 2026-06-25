<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Redirect home page to dashboard or login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Authenticated Routes (Requires Auth but vault might be locked)
Route::middleware('auth')->group(function () {
    Route::get('/unlock', [AuthController::class, 'showUnlock'])->name('unlock');
    Route::post('/unlock', [AuthController::class, 'unlock'])->name('unlock.verify');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout']); // fallback GET logout for easy use
});

// Authenticated & Decryption Key Active Routes (Vault Unlocked)
Route::middleware(['auth', 'twofish.key'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/playground', [DashboardController::class, 'playground'])->name('playground');

    // Credentials CRUD
    Route::get('/credentials', [CredentialController::class, 'index'])->name('credentials.index');
    Route::post('/credentials', [CredentialController::class, 'store'])->name('credentials.store');
    Route::put('/credentials/{credential}', [CredentialController::class, 'update'])->name('credentials.update');
    Route::delete('/credentials/{credential}', [CredentialController::class, 'destroy'])->name('credentials.destroy');

    // Decrypt API (AJAX)
    Route::post('/credentials/{credential}/decrypt', [CredentialController::class, 'decrypt'])->name('credentials.decrypt');

    // Sandbox API (AJAX)
    Route::post('/sandbox/encrypt', [DashboardController::class, 'sandboxEncrypt'])->name('sandbox.encrypt');
    Route::post('/sandbox/decrypt', [DashboardController::class, 'sandboxDecrypt'])->name('sandbox.decrypt');
});
