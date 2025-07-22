<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class RoleController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        return $this->transactionService->handleWithTransaction(function () {
            $roles = Role::all();

            return response()->json([
                'success' => true,
                'message' => 'Daftar role berhasil diambil.',
                'data' => $roles
            ]);
        }, 'List Role');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_role' => 'required|string|max:50|unique:role,nama_role',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($validated) {
            $role = Role::create($validated);

            $this->transactionService->handleWithLogDB('Create Role', 'role', $role->id_role, json_encode($validated));

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil ditambahkan.',
                'data' => $role
            ], 201);
        }, 'Create Role');
    }

    public function show($id_role)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_role) {
            $role = Role::find($id_role);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail role berhasil diambil.',
                'data' => $role
            ]);
        }, 'Detail Role');
    }

    public function update(Request $request, $id_role)
    {
        $validated = $request->validate([
            'nama_role' => 'required|string|max:50|unique:role,nama_role,' . $id_role . ',id_role',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($id_role, $validated) {
            $role = Role::find($id_role);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role tidak ditemukan.'
                ], 404);
            }

            $role->update($validated);

            $this->transactionService->handleWithLogDB('Update Role', 'role', $role->id_role, json_encode($validated));

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil diupdate.',
                'data' => $role
            ]);
        }, 'Update Role');
    }

    public function destroy($id_role)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id_role) {
            $role = Role::find($id_role);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role tidak ditemukan.'
                ], 404);
            }

            try {
                $this->transactionService->handleWithLogDB('Delete Role', 'role', $id_role, $role);
                $role->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Role berhasil dihapus.'
                ]);
            } catch (QueryException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus role: role sedang digunakan di data lain.',
                    'error' => $e->getMessage()
                ], 409);
            }
        }, 'Delete Role');
    }
}
