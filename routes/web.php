<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengajuanController;

Route::get('/', function () {
    return view('tampilan.utama');
})->name('utama');

Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');

Route::view('/register', 'auth.register')->name('register');
Route::view('/verifikasi', 'auth.verifikasi')->name('verifikasi');
Route::view('/login', 'auth.login')->name('login');

Route::view('/forgot-password', 'auth.forgot-password')->name('forgot-password');
Route::view('/verifikasi-otp', 'auth.verifikasi-otp')->name('verifikasi-otp');
Route::view('/reset-password', 'auth.reset-password')->name('reset-password');
 
Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::prefix('role')->name('role.')->group(function () {
        Route::view('/', 'admin.role.index')->name('index');
        Route::view('/create', 'admin.role.create')->name('create');
        Route::get('/{id_role}/edit', fn($id_role) => view('admin.role.edit', compact('id_role')))->name('edit');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::view('/', 'admin.user.index')->name('index');
        Route::view('/create', 'admin.user.create')->name('create');
        Route::get('/{id}/edit', fn($id) => view('admin.user.edit', compact('id')))->name('edit');
    });

    Route::prefix('workshop')->name('workshop.')->group(function () {
        Route::view('/', 'admin.workshop.index')->name('index');
        Route::view('/create', 'admin.workshop.create')->name('create');
        Route::get('/{id_workshop}/edit', fn($id_workshop) => view('admin.workshop.edit', compact('id_workshop')))->name('edit');
    });

    Route::prefix('pelayanan')->name('pelayanan.')->group(function () {
        Route::view('/', 'admin.pelayanan.index')->name('index');
        Route::view('/create', 'admin.pelayanan.create')->name('create');
        Route::get('/{id_pelayanan}/edit', fn($id_pelayanan) => view('admin.pelayanan.edit', compact('id_pelayanan')))->name('edit');
    });

    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::view('/', 'admin.pengajuan.index')->name('index');
        Route::view('/create', 'admin.pengajuan.create')->name('create');
        Route::get('/{id_pengajuan}/edit', fn($id_pengajuan) => view('admin.pengajuan.edit', compact('id_pengajuan')))->name('edit');
        Route::get('/{id_pengajuan}', fn($id_pengajuan) => view('admin.pengajuan.show', compact('id_pengajuan')))->name('show');
    });

    Route::prefix('verifikator')->name('verifikator.')->group(function () {
        Route::view('/', 'admin.verifikator.index')->name('index');
        Route::view('/create', 'admin.verifikator.create')->name('create');
        Route::get('/{id_verifikator}/edit', fn($id_verifikator) => view('admin.verifikator.edit', compact('id_verifikator')))->name('edit');
    });

    Route::prefix('logs')->name('logs.')->group(function () {
        Route::view('/logactivity', 'admin.logs.logactivity')->name('logactivity');
        Route::view('/logdatabase', 'admin.logs.logdatabase')->name('logdatabase');
        Route::view('/logerror', 'admin.logs.logerror')->name('logerror');
        Route::view('/logapproval', 'admin.logs.logapproval')->name('logapproval');
    });
});

Route::prefix('verifikator')->name('verifikator.')->group(function () {
    Route::view('/dashboard', 'verifikator.dashboard')->name('dashboard');

    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::view('/', 'verifikator.pengajuan.index')->name('index');
        Route::get('/{id_pengajuan}/show', fn($id_pengajuan) => view('verifikator.pengajuan.show', compact('id_pengajuan')))->name('show');
    });
});

Route::prefix('peserta')->name('peserta.')->group(function () {
    Route::view('/dashboard', 'peserta.dashboard')->name('dashboard');

    Route::prefix('workshop')->name('workshop.')->group(function () {
        Route::view('/', 'peserta.workshop.index')->name('index');
        Route::get('/{id_workshop}', fn($id_workshop) => view('peserta.workshop.show', compact('id_workshop')))->name('show');
    });

    Route::prefix('pelayanan')->name('pelayanan.')->group(function () {
        Route::view('/', 'peserta.pelayanan.index')->name('index');
        Route::get('/{id_pelayanan}', fn($id_pelayanan) => view('peserta.pelayanan.show', compact('id_pelayanan')))->name('show');
    });

    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::view('/', 'peserta.pengajuan.index')->name('index');
        Route::view('/create', 'peserta.pengajuan.create')->name('create');
        Route::get('/{id_pengajuan}', fn($id_pengajuan) => view('peserta.pengajuan.show', compact('id_pengajuan')))->name('show');
    });
});
