<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
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

// Guest & Verification Routes
Route::get('/register/verify', [AuthController::class, 'showVerifyForm'])->name('register.verify.form');
Route::post('/register/verify', [AuthController::class, 'verifyOtp'])->name('register.verify.submit');
Route::get('/login/2fa', [AuthController::class, 'show2FaChallenge'])->name('login.2fa.form');
Route::post('/login/2fa', [AuthController::class, 'verify2FaChallenge'])->name('login.2fa.submit');

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
    
    // Google 2FA Setup
    Route::get('/google2fa/setup', [AuthController::class, 'show2FaSetup'])->name('google2fa.setup');
    Route::post('/google2fa/setup', [AuthController::class, 'enable2Fa'])->name('google2fa.enable');

    // Credentials CRUD
    Route::get('/credentials', [CredentialController::class, 'index'])->name('credentials.index');
    Route::post('/credentials', [CredentialController::class, 'store'])->name('credentials.store');
    Route::put('/credentials/{credential}', [CredentialController::class, 'update'])->name('credentials.update');
    Route::delete('/credentials/{credential}', [CredentialController::class, 'destroy'])->name('credentials.destroy');

    // Backup & Restore
    Route::get('/credentials/backup/export', [CredentialController::class, 'exportBackup'])->name('credentials.backup.export');
    Route::post('/credentials/backup/import', [CredentialController::class, 'importBackup'])->name('credentials.backup.import');

    // Decrypt API (AJAX)
    Route::post('/credentials/{credential}/decrypt', [CredentialController::class, 'decrypt'])->name('credentials.decrypt');

    // Sandbox API (AJAX)
    Route::post('/sandbox/encrypt', [DashboardController::class, 'sandboxEncrypt'])->name('sandbox.encrypt');
    Route::post('/sandbox/decrypt', [DashboardController::class, 'sandboxDecrypt'])->name('sandbox.decrypt');

    // User Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});

// Admin Routes (Requires Auth & Admin role)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports.index');
    Route::get('/admin/reports/{report}', [AdminController::class, 'showReport'])->name('admin.reports.show');
    Route::post('/admin/reports/{report}/reply', [AdminController::class, 'replyReport'])->name('admin.reports.reply');
});
