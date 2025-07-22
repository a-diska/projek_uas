<?php

namespace App\Http\Controllers;

use App\Mail\OTP;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:user,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = User::where('email', $request->email)->first();

            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new OTP($otp));

            return response()->json([
                'success' => true,
                'message' => 'OTP berhasil dikirim ke email.',
            ]);
        }, 'forgot-password');
    }

    public function verifikasi(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'OTP wajib diisi.',
            'otp.size' => 'OTP harus terdiri dari 6 digit.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = User::where('otp', $request->otp)
                        ->where('otp_expires_at', '>', now())
                        ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP tidak valid atau telah kedaluwarsa.',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP berhasil diverifikasi.',
                'data' => [
                    'id_user' => $user->id
                ]
            ]);
        }, 'verifikasi-otp');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:user,id',
            'password' => 'required|confirmed|min:6',
        ], [
            'id.required' => 'ID pengguna tidak ditemukan.',
            'id.exists' => 'ID pengguna tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = User::find($request->id);

            $user->update([
                'password' => bcrypt($request->password),
                'otp' => null,
                'otp_expires_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset.',
            ]);
        }, 'reset-password');
    }
}