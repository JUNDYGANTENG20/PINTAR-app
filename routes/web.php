<?php

use App\Http\Controllers\Admin\KegiatanController as AdminKegiatanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\PengingatController;
use Illuminate\Support\Facades\Route;

// Halaman publik (mahasiswa & tamu)
Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
Route::get('/kegiatan/{kegiatan}', [KegiatanController::class, 'show'])->name('kegiatan.show');
Route::get('/beasiswa', [KegiatanController::class, 'beasiswa'])->name('beasiswa');
Route::view('/tentang', 'tentang')->name('tentang');

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'create'])->name('login');
    Route::post('/masuk', [LoginController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/daftar', [RegisterController::class, 'create'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'store'])->middleware('throttle:10,1');
});
Route::post('/keluar', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Pengingat deadline (FR-05) — butuh login
Route::middleware('auth')->group(function () {
    Route::get('/pengingat', [PengingatController::class, 'index'])->name('pengingat.index');
    Route::post('/kegiatan/{kegiatan}/pengingat', [PengingatController::class, 'store'])->name('pengingat.store');
    Route::delete('/kegiatan/{kegiatan}/pengingat', [PengingatController::class, 'destroy'])->name('pengingat.destroy');
});

// Admin / pengelola
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('kegiatan', AdminKegiatanController::class)->except('show');
    Route::patch('kegiatan/{kegiatan}/verifikasi', [AdminKegiatanController::class, 'verifikasi'])->name('kegiatan.verifikasi');
});
