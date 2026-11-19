<?php

use App\Http\Controllers\AdventureController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// Home landing redirect
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Character Management (CRUD)
    Route::resource('characters', CharacterController::class);

    // Campaign Management
    Route::resource('campaigns', CampaignController::class)->except(['edit', 'update']);

    // Interactive AI Adventure & AJAX Game Endpoints
    Route::get('/campaigns/{campaign}/adventure', [CampaignController::class, 'adventure'])->name('campaigns.adventure');
    Route::post('/campaigns/{campaign}/action', [AdventureController::class, 'takeAction'])->name('campaigns.action');
    Route::post('/campaigns/{campaign}/use-item', [AdventureController::class, 'useItem'])->name('campaigns.use-item');
});
