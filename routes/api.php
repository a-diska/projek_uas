<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PelayananController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\ApprovalPengajuanController;
use App\Http\Controllers\MailerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VerifikatorController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ForgotPasswordController;

Route::get('/dashboard', [DashboardController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verifikasi', [MailerController::class, 'verifikasi']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

Route::prefix('forgot-password')->group(function () {
    Route::post('/send', [ForgotPasswordController::class, 'send']);
    Route::post('/verifikasi-otp', [ForgotPasswordController::class, 'verifikasi']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'getProfile']);

    Route::prefix('role')->middleware('role:admin')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{id_role}', [RoleController::class, 'show']);
        Route::put('/{id_role}', [RoleController::class, 'update']);
        Route::delete('/{id_role}', [RoleController::class, 'destroy']);
    });

    Route::prefix('user')->middleware('role:admin')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id_user}', [UserController::class, 'show']);
        Route::put('/{id_user}', [UserController::class, 'update']);
        Route::delete('/{id_user}', [UserController::class, 'destroy']);
    });

    Route::prefix('verifikator')->middleware('role:admin')->group(function () {
        Route::get('/', [VerifikatorController::class, 'index']);
        Route::post('/', [VerifikatorController::class, 'store']);
        Route::get('/{id_verifikator}', [VerifikatorController::class, 'show']);
        Route::put('/{id_verifikator}', [VerifikatorController::class, 'update']);
        Route::delete('/{id_verifikator}', [VerifikatorController::class, 'destroy']);
    });

    Route::prefix('workshop')->group(function () {
        Route::get('/', [WorkshopController::class, 'index']);
        Route::get('/{id_workshop}', [WorkshopController::class, 'show']);

        Route::middleware('role:admin')->group(function () {
            Route::post('/', [WorkshopController::class, 'store']);
            Route::put('/{id_workshop}', [WorkshopController::class, 'update']);
            Route::delete('/{id_workshop}', [WorkshopController::class, 'destroy']);
        });
    });

    Route::prefix('pelayanan')->group(function () {
        Route::get('/', [PelayananController::class, 'index']);
        Route::get('/{id_pelayanan}', [PelayananController::class, 'show']);

        Route::middleware('role:admin')->group(function () {
            Route::post('/', [PelayananController::class, 'store']);
            Route::put('/{id_pelayanan}', [PelayananController::class, 'update']);
            Route::delete('/{id_pelayanan}', [PelayananController::class, 'destroy']);
        });
    });

    Route::prefix('pengajuan')->group(function () {
        Route::get('/', [PengajuanController::class, 'index']);
        Route::get('/{id_pengajuan}', [PengajuanController::class, 'show']);
        Route::middleware('role:admin')->group(function () {
            Route::post('/', [PengajuanController::class, 'store']);
            Route::put('/{id_pengajuan}', [PengajuanController::class, 'update']);
        });
        Route::middleware('role:peserta')->group(function () {
        Route::post('/peserta', [PengajuanController::class, 'storePeserta']);
        });
        Route::middleware('role:peserta,admin')->group(function () {
            Route::delete('/{id_pengajuan}', [PengajuanController::class, 'destroy']);
        });
    });

    Route::prefix('dokumen')->group(function () {
        Route::get('/', [DokumenController::class, 'index']);
        Route::get('/{id_dokumen}', [DokumenController::class, 'show']);
        Route::put('/{id_dokumen}', [DokumenController::class, 'update']);

        Route::middleware('role:admin,peserta')->group(function () {
            Route::post('/', [DokumenController::class, 'store']);
        });
        Route::middleware('role:admin')->group(function () {
            Route::delete('/{id_dokumen}', [DokumenController::class, 'destroy']);
        });
    });

   Route::prefix('approval')->middleware('role:verifikator')->group(function () {
        Route::get('/', [ApprovalPengajuanController::class, 'index']);
        Route::post('/{id_pengajuan}/approve', [ApprovalPengajuanController::class, 'approve']); 
    });

    Route::prefix('logs')->middleware('role:admin')->group(function () {
        Route::get('/activity', [LogsController::class, 'activity']);
        Route::get('/database', [LogsController::class, 'database']);
        Route::get('/error', [LogsController::class, 'error']);
        Route::get('/approval', [LogsController::class, 'approval']);
    });
});
