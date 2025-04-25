<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelSearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomTypeController;
use Illuminate\Foundation\Application;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('pro.dashboard');

    Route::post('/hotel/information', [HotelController::class, 'store'])->name('hotel.store');
    Route::post('/hotel/{id}', [HotelController::class, 'update'])->name('hotel.update');
    Route::get('/hotel/{id}/edit', [HotelController::class, 'edit'])->name('hotel.edit');
    Route::get('/hotel/information', [HotelController::class, 'create'])->name('hotel.create');

    Route::get('/room-types', [RoomTypeController::class, 'index'])->name('room-types.index');
    Route::get('/room-types/create', [RoomTypeController::class, 'create'])->name('room-types.create');
    Route::post('/room-types', [RoomTypeController::class, 'store'])->name('room-types.store');
    Route::get('/room-types/{roomType}/edit', [RoomTypeController::class, 'edit'])->name('room-types.edit');
    Route::post('/room-types/{roomType}', [RoomTypeController::class, 'update'])->name('room-types.update');
    Route::delete('/room-types/{roomType}', [RoomTypeController::class, 'destroy'])->name('room-types.destroy');

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

Route::get('/map', function () {
    return Inertia::render('MapMatch');
});
Route::get('/map', [HotelSearchController::class, 'search']);




require __DIR__ . '/auth.php';
