<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; 
use App\Models\User;
use App\Mail\OTP; 
use App\Services\TransactionService;

class AuthController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_telp' => 'required|string|max:13',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|confirmed|min:6',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'no_telp.required' => 'Nomor telepon wajib diisi.',
            'no_telp.max' => 'Nomor telepon maksimal 13 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $user = User::create([
                'id_role' => 3, // ubah sesuai default role
                'nama' => $request->nama,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new OTP($otp));

            return response()->json([
                'success' => true,
                'message' => 'Register berhasil, OTP dikirim ke email.',
            ], 201);
        }, 'register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6'
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = User::with('role')->where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.'
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $user->tokens()->latest()->first()->update([
                'expires_at' => now()->addHours(6)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data' => [
                    'token' => 'Bearer ' . $token,
                    'user' => [
                        'id'    => $user->id,
                        'nama'  => $user->nama,
                        'email' => $user->email,
                        'role'  => $user->role->nama_role ?? null,
                    ]
                ]
            ], 200);
        }, 'Login');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.'
        ]);
    }

    public function profile(Request $request)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = $request->user()->load('role');

            return response()->json([
                'success' => true,
                'user' => [
                    'id'    => $user->id,
                    'nama'  => $user->nama,
                    'email' => $user->email,
                    'role'  => $user->role->nama_role ?? null,
                ]
            ], 200);
        }, 'Get Profile');
    }
}
