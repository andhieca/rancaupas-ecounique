<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VisitorController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tourism/detail/{tourism}', [HomeController::class, 'detail'])->name('tourism.detail');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.update')->middleware('guest');

// Visitor (Pengunjung) Routes
Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/visitor', [VisitorController::class, 'index'])->name('visitor.dashboard');
    Route::post('/visitor/rate', [VisitorController::class, 'rate'])->name('visitor.rate');
    Route::get('/visitor/detail/{tourism}', [VisitorController::class, 'detail'])->name('visitor.detail');
});

Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\IsAdmin::class])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/export-saw', [AdminController::class, 'exportSaw'])->name('admin.export.saw');
    Route::resource('tourism', \App\Http\Controllers\TourismController::class)->only(['store', 'update', 'destroy']);
    Route::resource('settings', \App\Http\Controllers\SettingController::class)->only(['store', 'update', 'destroy']);
    Route::resource('criteria', \App\Http\Controllers\CriterionController::class)->only(['store', 'update', 'destroy']);
});

