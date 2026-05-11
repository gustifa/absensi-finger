<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Tambahkan baris ini
    Route::post('/dashboard/tarik-manual', [DashboardController::class, 'tarikManual'])->name('tarik.manual');

    // Route untuk fitur input manual guru
    Route::get('/dashboard/input-manual', [DashboardController::class, 'formKehadiranManual'])->name('kehadiran.manual');
    Route::post('/dashboard/input-manual', [DashboardController::class, 'simpanKehadiranManual'])->name('kehadiran.store');

    // Route untuk menarik data USER dari mesin
    Route::post('/dashboard/tarik-user', [DashboardController::class, 'tarikUserMesin'])->name('tarik.user');

    // Route untuk cek koneksi mesin
    Route::post('/dashboard/cek-koneksi', [DashboardController::class, 'cekKoneksiMesin'])->name('koneksi.cek');

    // Route untuk sinkronisasi push user ke mesin
    Route::get('/dashboard/tambah-user', [DashboardController::class, 'formTambahUser'])->name('user.create');
    Route::post('/dashboard/tambah-user', [DashboardController::class, 'simpanUser'])->name('user.store');

    Route::get('/dashboard/absensi-manual', [DashboardController::class, 'formAbsensiManual'])->name('absensi.manual');
    Route::post('/dashboard/absensi-manual', [DashboardController::class, 'simpanAbsensiManual'])->name('absensi.simpan');

// Route untuk Master Data Semua Absensi
    Route::get('/dashboard/seluruh-absensi', [DashboardController::class, 'seluruhAbsensi'])->name('absensi.semua');

    // Route untuk melihat daftar seluruh user
    Route::get('/dashboard/daftar-user', [DashboardController::class, 'daftarUser'])->name('user.daftar');
});
