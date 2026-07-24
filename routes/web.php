<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;

// Home
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/guru/{user}/verify', [AdminController::class, 'verifyGuru'])->name('admin.verify');
    Route::post('/guru/{user}/reject', [AdminController::class, 'rejectGuru'])->name('admin.reject');
});

// Guru Routes
Route::prefix('guru')->middleware(['auth', 'guru'])->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('guru.dashboard');
    
    // Soal Routes
    Route::get('/soal', [GuruController::class, 'listSoal'])->name('guru.soal.list');
    Route::get('/soal/create', [GuruController::class, 'createSoal'])->name('guru.soal.create');
    Route::post('/soal', [GuruController::class, 'storeSoal'])->name('guru.soal.store');
    Route::get('/soal/{soal}/edit', [GuruController::class, 'editSoal'])->name('guru.soal.edit');
    Route::put('/soal/{soal}', [GuruController::class, 'updateSoal'])->name('guru.soal.update');
    Route::delete('/soal/{soal}', [GuruController::class, 'deleteSoal'])->name('guru.soal.delete');
    
    // OCR Routes
    Route::get('/soal/scan/upload', [GuruController::class, 'showScanUpload'])->name('guru.soal.scan.upload');
    Route::post('/soal/scan/process', [GuruController::class, 'processScanImage'])->name('guru.soal.scan.process');
    Route::get('/soal/scan/review', [GuruController::class, 'showScanReview'])->name('guru.soal.scan.review');
    Route::post('/soal/scan/save', [GuruController::class, 'saveScanResult'])->name('guru.soal.scan.save');
});

