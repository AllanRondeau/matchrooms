<?php

use App\Http\Controllers\HotelController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LandingController;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/Welcome', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return Inertia::render('Home');
    })->middleware(['auth', 'verified'])->name('home');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:hotelier'])->prefix('hotelier')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Pro/Dashboard');
    })->name('pro.dashboard');

    Route::post('/hotel/information', [HotelController::class, 'store'])->name('hotel.store');
    Route::post('/hotel/{id}', [HotelController::class, 'update'])->name('hotel.update');
    Route::get('/hotel/{id}/edit', [HotelController::class, 'edit'])->name('hotel.edit');
    Route::get('/hotel/information', [HotelController::class, 'create'])->name('hotel.create');

    Route::get('/rooms', function () {
        return Inertia::render('Pro/Rooms/Index');
    })->name('pro.rooms.index');

    Route::get('/rooms/create', function () {
        return Inertia::render('Pro/Rooms/Create');
    })->name('pro.rooms.create');

    Route::get('/reservations', function () {
        return Inertia::render('Pro/Reservations/Index');
    })->name('pro.reservations.index');

    Route::get('/statistics', function () {
        return Inertia::render('Pro/Statistics');
    })->name('pro.statistics');
});

require __DIR__ . '/auth.php';
