<?php

namespace App\Http\Controllers;

use App\Mail\OTP;
use App\Models\User;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailerController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function verifikasi(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = User::where('otp', $request->otp)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP tidak cocok atau sudah tidak berlaku',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi OTP berhasil',
                'data' => $user
            ]);
        }, 'Verifikasi OTP');
    }

    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'Email tidak ditemukan'], 404);
            }

            if ($user->email_verified_at) {
                return response()->json(['message' => 'Email sudah diverifikasi.'], 400);
            }

            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            try {
                Mail::to($user->email)->send(new OTP($otp));
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Gagal mengirim email: ' . $e->getMessage(),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP berhasil dikirim ulang',
            ]);
        }, 'Resend OTP');
    }
}
