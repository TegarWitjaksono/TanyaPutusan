<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PutusanScraperController;

// Halaman Beranda (Menampilkan data putusan terkumpul)
Route::get('/', [PutusanScraperController::class, 'index'])->name('home');

// Halaman Yurisprudensi (Kaidah Hukum & Landmark Decision)
Route::get('/yurisprudensi', [PutusanScraperController::class, 'yurisprudensi'])->name('yurisprudensi');

// Halaman Tren Putusan 2026 (Analisis & Statistik)
Route::get('/tren-putusan', [PutusanScraperController::class, 'trenPutusan'])->name('tren');

// Trigger Scraper Python secara real-time via API/AJAX
Route::post('/api/scrape', [PutusanScraperController::class, 'triggerScrape'])->name('api.scrape');

// Mendapatkan data hasil scraping dalam format JSON
Route::get('/api/putusan', [PutusanScraperController::class, 'getData'])->name('api.putusan');

// Endpoint Live Streaming Data Putusan Langsung dari Portal MA (Zero Storage)
Route::get('/api/live', [PutusanScraperController::class, 'liveData'])->name('api.live');

// Pratinjau Cepat Ringkasan & Kaidah Putusan / Yurisprudensi (In-App Preview)
Route::get('/putusan/preview', [PutusanScraperController::class, 'previewDetail'])->name('putusan.preview');

// Stream & Tampilkan Langsung Dokumen PDF Putusan MA (Tanpa Buka Portal MA)
Route::get('/putusan/pdf', [PutusanScraperController::class, 'viewPdf'])->name('putusan.pdf');

// Authentication Routes
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/api/auth/check', [AuthController::class, 'checkAuth'])->name('api.auth.check');

// Google OAuth Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// AI Legal Assistant Chat Route (Membutuhkan Login)
Route::post('/api/ai/chat', [PutusanScraperController::class, 'aiChat'])->name('api.ai.chat')->middleware('auth');
