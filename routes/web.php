<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BimbinganController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanMingguanController;
use Illuminate\Support\Facades\Route;

// Redirect URL admin Backpack lama ke halaman awal (login)
Route::redirect('/admin/login', '/');
Route::redirect('/admin', '/');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::redirect('/login', '/');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users CRUD
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::post('/', [UserController::class, 'store'])->name('user.store');
        Route::put('/{user}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    // Bimbingan CRUD
    Route::prefix('bimbingan')->group(function () {
        Route::get('/', [BimbinganController::class, 'index'])->name('bimbingan.index');
        Route::get('/{bimbingan}/komentar', [BimbinganController::class, 'komentarHistory'])->name('bimbingan.komentar');
        Route::post('/', [BimbinganController::class, 'store'])->name('bimbingan.store');
        Route::put('/{bimbingan}', [BimbinganController::class, 'update'])->name('bimbingan.update');
        Route::delete('/{bimbingan}', [BimbinganController::class, 'destroy'])->name('bimbingan.destroy');
    });

    // Laporan CRUD
    Route::prefix('laporan')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
        Route::post('/', [LaporanController::class, 'store'])->name('laporan.store');
        Route::put('/{laporan}', [LaporanController::class, 'update'])->name('laporan.update');
        Route::delete('/{laporan}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
    });

    // Laporan Mingguan CRUD
    Route::prefix('laporan-mingguan')->group(function () {
        Route::get('/', [LaporanMingguanController::class, 'index'])->name('laporan-mingguan.index');
        Route::post('/', [LaporanMingguanController::class, 'store'])->name('laporan-mingguan.store');
        Route::put('/{laporanMingguan}', [LaporanMingguanController::class, 'update'])->name('laporan-mingguan.update');
        Route::delete('/{laporanMingguan}', [LaporanMingguanController::class, 'destroy'])->name('laporan-mingguan.destroy');
    });
});
