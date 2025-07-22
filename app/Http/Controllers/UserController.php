<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    // Menampilkan semua user (dengan filter role & optional hanya id/nama)
    public function index(Request $request)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $query = User::with('role');

            // Filter berdasarkan role jika diberikan
            if ($request->has('role')) {
                $query->whereHas('role', function ($q) use ($request) {
                    $q->where('nama_role', $request->role);
                });
            }

            // Hanya ambil id dan nama (untuk dropdown)
            if ($request->filled('only') && $request->only === 'id_nama') {
                $users = $query->select('id', 'nama')->get();
            } else {
                $users = $query->get();
            }

            return response()->json([
                'success' => true,
                'message' => 'List semua data user',
                'data' => $users
            ]);
        }, 'List User');
    }

    // Menyimpan user baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_role' => 'required|exists:role,id_role',
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:12',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $data = [
                'id_role' => $request->id_role,
                'nama' => $request->nama,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];

            $user = User::create($data);

            $this->transactionService->handleWithLogDB('Create User', 'user', $user->id, json_encode($data));

            return response()->json([
                'success' => true,
                'message' => 'User berhasil disimpan.',
                'data' => $user
            ], 201);
        }, 'Create User');
    }

    // Menampilkan detail user
    public function show($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $user = User::with('role')->find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }, 'Detail User');
    }

    // Mengupdate data user
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_role' => 'exists:role,id_role',
            'nama' => 'string|max:255',
            'no_telp' => 'string|max:12',
            'email' => 'email|unique:user,email,' . $id,
            'password' => 'nullable|string|min:6',
            'otp' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        return $this->transactionService->handleWithTransaction(function () use ($request, $id) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
            }

            $data = [
                'id_role' => $request->id_role ?? $user->id_role,
                'nama' => $request->nama ?? $user->nama,
                'no_telp' => $request->no_telp ?? $user->no_telp,
                'email' => $request->email ?? $user->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
                'otp' => $request->otp ?? $user->otp,
            ];

            $user->update($data);

            $this->transactionService->handleWithLogDB('Update User', 'user', $user->id, json_encode($data));

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'data' => $user
            ]);
        }, 'Update User');
    }

    // Menghapus user
    public function destroy($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
            }

            $this->transactionService->handleWithLogDB('Delete User', 'user', $id, $user);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        }, 'Delete User');
    }

    // Ambil semua user dengan hanya id & nama (opsional kalau kamu pakai)
    public function all()
    {
        return $this->transactionService->handleWithTransaction(function () {
            $users = User::select('id', 'nama')->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        }, 'List Nama User');
    }

    public function getProfile(Request $request)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = $request->user()->load('role');

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'role' => $user->role->nama_role ?? null,
                ]
            ], 200);
        }, 'get-profile');
    }
}
