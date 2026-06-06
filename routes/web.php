<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\NasabahController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PengepulController;
use App\Http\Controllers\Admin\PenjualanSampahController;
use App\Http\Controllers\Admin\SampahController;
use App\Http\Controllers\Admin\SetoranController;
// use App\Http\Controllers\Admin\StokSampahController;
use App\Http\Controllers\Admin\GoldExchangeController as AdminGoldExchangeController;
use App\Http\Controllers\UserController;
use App\Models\JenisSampah;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $jenisSampah = JenisSampah::all();
    $nasabah = null;
    if (Auth::check()) {
        $nasabah = Auth::user()?->nasabah;
    }
    return view('landing', compact('jenisSampah', 'nasabah'));
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data & Transaksi
    Route::resource('nasabah', NasabahController::class);
    Route::get('/nasabah-search', [NasabahController::class, 'search'])->name('nasabah.search');

    Route::resource('pengepul', PengepulController::class);
    Route::get('/pengepul-search', [PengepulController::class, 'search'])->name('pengepul.search');

    Route::resource('sampah', SampahController::class);
    Route::get('/sampah-search', [SampahController::class, 'search'])->name('sampah.search');

    Route::resource('setoran', SetoranController::class);
    Route::get('/get-sampah-by-jenis/{id}', [SetoranController::class, 'getSampahByJenis']);
    Route::get('/setoran/laporan/pdf', [SetoranController::class, 'laporanPDF'])->name('setoran.laporan.pdf');
    Route::post('/setoran/{id}/void', [SetoranController::class, 'void'])->name('setoran.void');

    Route::resource('penjualan', PenjualanSampahController::class);
    Route::get('/penjualan/laporan', [PenjualanSampahController::class, 'laporanPDF'])->name('penjualan.laporan');
    Route::post('/penjualan/{id}/void', [PenjualanSampahController::class, 'void'])->name('penjualan.void');

    // Gold Exchange
    Route::get('/gold-exchange', [AdminGoldExchangeController::class, 'index'])->name('gold-exchange.index');
    Route::post('/gold-exchange/{id}/approve', [AdminGoldExchangeController::class, 'approve'])->name('gold-exchange.approve');
    Route::post('/gold-exchange/{id}/reject', [AdminGoldExchangeController::class, 'reject'])->name('gold-exchange.reject');
});

// Nasabah Routes
Route::middleware(['auth', 'role:nasabah'])->prefix('nasabah')->name('nasabah.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Nasabah\DashboardController::class, 'index'])->name('dashboard');

    // Riwayat & Saldo
    Route::get('/riwayat-transaksi', [App\Http\Controllers\Nasabah\TransaksiController::class, 'index'])->name('riwayat-transaksi');
    Route::get('/info-saldo', [App\Http\Controllers\Nasabah\DashboardController::class, 'infoSaldo'])->name('info-saldo');

    // Tukar Emas
    Route::get('/tukar-emas', [App\Http\Controllers\Nasabah\GoldExchangeController::class, 'index'])->name('gold-exchange.index');
    Route::get('/tukar-emas/create', [App\Http\Controllers\Nasabah\GoldExchangeController::class, 'create'])->name('gold-exchange.create');
    Route::post('/tukar-emas', [App\Http\Controllers\Nasabah\GoldExchangeController::class, 'store'])->name('gold-exchange.store');

    // Profil
    Route::get('/profile', [App\Http\Controllers\Nasabah\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Nasabah\ProfileController::class, 'update'])->name('profile.update');
});

// Common Auth Routes (Settings)
Route::middleware(['auth'])->group(function () {
    Route::get('/settings', [UserController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [UserController::class, 'update'])->name('settings.update');
    Route::post('/settings/password', [UserController::class, 'updatePassword'])->name('settings.updatePassword');
});
