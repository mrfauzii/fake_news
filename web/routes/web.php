<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\PencarianController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UmpanBalikController;
use App\Http\Controllers\WaController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DetectionController;
use App\Http\Controllers\ImageDetectionController;
use App\Http\Controllers\PopulerHistoryController;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\HistoryManagementController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ScraperController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('landing_page.landing');
})->name('beranda');

// Pencarian (public)
Route::get('/pencarian', [PencarianController::class, 'index'])->name('deteksi');
Route::post('/telusuri', [PencarianController::class, 'telusuri'])->name('telusuri');
Route::post('/telusuri-gambar', [ImageDetectionController::class, 'detect'])->name('telusuri.gambar');
Route::get('/pencarian/populer', [PopulerHistoryController::class, 'index'])->name('pencarian.populer');

// WhatsApp Page
Route::get('/dapatkan-whatsapp', function () {
    return view('user.whatsapp');
})->name('whatsapp.page');


// API detect endpoint (single consolidated route)
Route::post('/api/detect', [DetectionController::class, 'detect'])->name('detect.hoax');

// Unduh CSV
Route::get('/riwayat/unduh-csv', [CsvController::class, 'unduhCsv'])->name('riwayat.unduh_csv');


Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.form');
Route::delete('/feedback', [FeedbackController::class, 'destroy'])->name('feedback.delete');


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

// Login Web
Route::get('/masuk', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/masuk', [LoginController::class, 'login'])->name('login.post');
Route::post('/keluar', [LoginController::class, 'logout'])->name('logout');

// Login via WhatsApp
Route::prefix('login-wa')->group(function () {
    Route::get('/', [AuthController::class, 'showPhoneForm']);
    Route::post('/request', [AuthController::class, 'requestToken']);
    Route::get('/verify', [AuthController::class, 'showTokenForm'])->name('login.wa.verify');
    Route::post('/verify', [AuthController::class, 'verifyToken']);
});

// Google Auth
Route::prefix('auth/google')->group(function () {
    Route::get('/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

// Route::get('/pencarian-terpopuler', function () {
//     return view('user.pencarian-terpopuler');
// })->name('pencarian.populer');
Route::post('/telusuri', [PencarianController::class, 'telusuri'])->name('telusuri');
Route::post('/telusuri-gambar', [PencarianController::class, 'telusuriGambar'])->name('telusuri.gambar');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (PROTECTED)
|--------------------------------------------------------------------------
*/

// Admin routes (kept unprotected here for later grouping) -- uncomment middleware when ready
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/user-data', [UserController::class, 'getUserData']);

    // Umpan Balik
    Route::get('/umpanbalik', [UmpanBalikController::class, 'index']);
    Route::get('/umpanbalik-data', [UmpanBalikController::class, 'getFeedbackData']);

    // Riwayat (admin side)
    Route::prefix('riwayat')->group(function () {
        Route::get('/', [RiwayatController::class, 'index']);
        Route::get('/edit/{id}', [RiwayatController::class, 'edit']);
        Route::post('/update/{id}', [RiwayatController::class, 'update']);
        Route::get('/delete/{id}', [RiwayatController::class, 'delete']);
        Route::post('/filter', [RiwayatController::class, 'filterRiwayat'])->name('riwayat.filter');
    });

    // Manajemen Riwayat
    Route::get('/history-management', [HistoryManagementController::class, 'index']);
    Route::get('/history-management/trash', [HistoryManagementController::class, 'trash']);
    Route::post('/history-management/soft-delete/{id}', [HistoryManagementController::class, 'softDelete']);
    Route::post('/history-management/restore/{id}', [HistoryManagementController::class, 'restore']);
    Route::delete('/history-management/hard-delete/{id}', [HistoryManagementController::class, 'hardDelete']);

    Route::get('/pencarian', [AdminController::class, 'pencarian'])->name('admin.pencarian');
    Route::get('/setting', [AdminController::class, 'setting'])->name('admin.setting');
    Route::get('/setting/scrape', [AdminController::class, 'settingScrape'])->name('admin.setting.scrape');
    Route::post('/setting/schedule-scrape', [ApiController::class, 'setScrapeSchedule'])->name('admin.setting.schedule-scrape');
});



// Admin logout 
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
/*
|--------------------------------------------------------------------------
| WHATSAPP WEBHOOK & API (NO AUTH)
|--------------------------------------------------------------------------
*/

Route::any('/wa-webhook', [WaController::class, 'webhook']);
Route::post('/detect-hoax', [ApiController::class, 'detectHoax']);

//route trigger scrapee
Route::get('/trigger-scraper', [ScraperController::class, 'triggerScraper']);


// Login using WhatsApp (web)
Route::get('/login-wa', [AuthController::class, 'showPhoneForm'])->name('login.wa');
Route::post('/login-wa/request', [AuthController::class, 'requestToken'])->name('login.wa.request');
Route::get('/login-wa/verify', [AuthController::class, 'showTokenForm'])->name('login.wa.verify');
Route::post('/login-wa/verify', [AuthController::class, 'verifyToken'])->name('login.wa.verify.post');

//verifikasi nomer wa
// Route untuk Verifikasi Nomor WA
Route::get('/verify-wa/{token}', [App\Http\Controllers\WaController::class, 'verifyWaLink'])->name('wa.verify.link');

/*
|--------------------------------------------------------------------------
| MOBILE NAVIGATION (simple endpoints so views can fetch correct nav)
| - `/navigation/mobile` serves a public mobile nav partial
| - `/navigation/mobile-auth` serves a mobile nav for authenticated users
| Note: create the corresponding views `partials.nav_mobile` and `partials.nav_mobile_auth`
|--------------------------------------------------------------------------
*/
Route::get('/navigation/mobile', function () {
    return view('partials.nav_mobile');
})->name('nav.mobile');

Route::get('/navigation/mobile-auth', function () {
    return view('partials.nav_mobile_auth');
})->middleware('auth')->name('nav.mobile.auth');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Update Profile
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

    // Link WhatsApp
    Route::post('/link-wa', [WaController::class, 'linkWhatsApp'])->name('wa.link');

    // Rute untuk halaman riwayat user (Digabung dan diubah namanya menjadi 'riwayat')
    Route::get('/riwayat-pencarian', [RiwayatController::class, 'riwayatUser'])->name('riwayat');

    // Rute untuk menghapus riwayat user secara spesifik (Soft Delete)
    Route::delete('/riwayat-saya/{id}', [RiwayatController::class, 'destroyUserHistory']);

    //Rute untuk menghapus semua riwayat user sekaligus
    Route::delete('/riwayat-saya', [RiwayatController::class, 'destroyAllUserHistory']);
});
