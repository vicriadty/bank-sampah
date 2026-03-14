<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengepulController;
use App\Http\Controllers\PenjualanSampahController;
use App\Http\Controllers\SampahController;
use App\Http\Controllers\SetoranController;
use App\Http\Controllers\TarikSaldoController;
use App\Http\Controllers\UserController;
use App\Models\JenisSampah;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $jenisSampah = JenisSampah::all();
    $nasabah = null;
    if (Auth::check()) {
        // Assuming the User model has a relationship to Nasabah model
        // and the nasabah is eager loaded or can be fetched like this.
        $nasabah = Auth::user()?->nasabah;
    }
    return view('pages.landing', compact('jenisSampah', 'nasabah'));
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');

//Nasabah Route
Route::get('/nasabah', [NasabahController::class, 'index'])->name('nasabah.index')->middleware('auth');
Route::get('/nasabah/create', [NasabahController::class, 'create'])->middleware('auth');
Route::get('/nasabah/{id}', [NasabahController::class, 'edit'])->middleware('auth');
Route::post('/nasabah', [NasabahController::class, 'store']);
Route::put('/nasabah/{id}', [NasabahController::class, 'update']);
Route::delete('/nasabah/{id}', [NasabahController::class, 'destroy']);
Route::get('/nasabah-search', [NasabahController::class, 'search'])->name('nasabah.search');

//Pengepul Route
Route::get('/pengepul', [PengepulController::class, 'index'])->name('pengepul.index')->middleware('auth');
Route::get('/pengepul/create', [PengepulController::class, 'create']);
Route::get('/pengepul/{id}', [PengepulController::class, 'edit']);
Route::post('/pengepul', [PengepulController::class, 'store']);
Route::put('/pengepul/{id}', [PengepulController::class, 'update']);
Route::delete('/pengepul/{id}', [PengepulController::class, 'destroy']);
Route::get('/pengepul-search', [PengepulController::class, 'search'])->name('pengepul.search');

//Sampah Route
Route::get('/sampah', [SampahController::class, 'indexSampah'])->name('sampah.index')->middleware('auth');
Route::get('/sampah/create', [SampahController::class, 'create'])->name('sampah.create')->middleware('auth');
Route::post('/sampah', [SampahController::class, 'store'])->name('sampah.store');
Route::get('/sampah/{id}', [SampahController::class, 'edit'])->middleware('auth');
Route::put('/sampah/{id}', [SampahController::class, 'update']);
Route::delete('/sampah/{id}', [SampahController::class, 'destroy']);

// Stok Sampah Route
Route::get('/stok-sampah', [SampahController::class, 'indexStokSampah'])->name('stok-sampah.index')->middleware('auth');

//Login Route
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Route::get('/', [DashboardController::class, 'index'])->middleware('auth');

//Register Route
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

//Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

//Setoran Sampah Route
Route::get('/setor-sampah', [SetoranController::class, 'index'])->name('setoran.index')->middleware('auth');
Route::get('/setor-sampah/create', [SetoranController::class, 'create'])->name('setoran.create')->middleware('auth');
Route::post('/setor-sampah', [SetoranController::class, 'store'])->name('setoran.store');
Route::get('/get-sampah-by-jenis/{id}', [SetoranController::class, 'getSampahByJenis']);

// Route::post('transaksi/setor-sampah', [SetoranController::class, 'store'])->name('setoran.store');

//Tarik Saldo Route
Route::get('/tarik-saldo', [TarikSaldoController::class, 'index'])->name('tarik-saldo.index')->middleware('auth');
Route::get('/tarik-saldo/create', [TarikSaldoController::class, 'create'])->name('tarik-saldo.create')->middleware('auth');
Route::post('/tarik-saldo', [TarikSaldoController::class, 'store'])->name('tarik-saldo.store');

//Laporan Route
Route::get('/setor-sampah/laporan/pdf', [SetoranController::class, 'laporanPDF'])->name('setoran.laporan.pdf');
Route::get('/tarik-saldo/laporan/pdf', [TarikSaldoController::class, 'laporanPDF'])->name('tarik-saldo.laporan.pdf');

//User Settings Route
Route::middleware(['auth'])->group(function () {
    Route::get('/settings', [UserController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [UserController::class, 'update'])->name('settings.update');
    Route::post('/settings/password', [UserController::class, 'updatePassword'])->name('settings.updatePassword');
});

//Penjualan Sampah Route
Route::get('/penjualan-sampah', [PenjualanSampahController::class, 'index'])->name('penjualan.index')->middleware('auth');
Route::get('/penjualan-sampah/create', [PenjualanSampahController::class, 'create'])->name('penjualan.create')->middleware('auth');
Route::post('/penjualan-sampah', [PenjualanSampahController::class, 'store'])->name('penjualan.store');
Route::get('/penjualan/laporan', [PenjualanSampahController::class, 'laporanPDF'])->name('penjualan.laporan');
Route::get('/get-sampah-by-jenis/{id}', [PenjualanSampahController::class, 'getSampahByJenis']);
