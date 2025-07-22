<?php

namespace App\Http\Controllers;

use App\Models\Pelayanan;
use Illuminate\Http\Request;
use App\Services\TransactionService;

class PelayananController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        return $this->transactionService->handleWithTransaction(function () {
            $pelayanan = Pelayanan::all();

            return response()->json([
                'success' => true,
                'message' => 'List semua pelayanan',
                'data' => $pelayanan
            ]);
        }, 'List Pelayanan');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelayanan' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'status' => 'required|in:aktif,nonaktif'
        ], [
            'nama_pelayanan.required' => 'Nama pelayanan wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status hanya boleh aktif atau nonaktif.'
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($validated) {
            $pelayanan = Pelayanan::create($validated);

            $this->transactionService->handleWithLogDB('Create Pelayanan', 'pelayanan', $pelayanan->id_pelayanan, json_encode($validated));

            return response()->json([
                'success' => true,
                'message' => 'Pelayanan berhasil ditambahkan!',
                'data' => $pelayanan
            ], 201);
        }, 'Create Pelayanan');
    }

    public function show($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $pelayanan = Pelayanan::find($id);

            if (!$pelayanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelayanan tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail pelayanan',
                'data' => $pelayanan
            ]);
        }, 'Detail Pelayanan');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_pelayanan' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'status' => 'required|in:aktif,nonaktif'
        ], [
            'nama_pelayanan.required' => 'Nama pelayanan wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status hanya boleh aktif atau nonaktif.'
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($id, $validated) {
            $pelayanan = Pelayanan::find($id);

            if (!$pelayanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelayanan tidak ditemukan.'
                ], 404);
            }

            $pelayanan->update($validated);

            $this->transactionService->handleWithLogDB('Update Pelayanan', 'pelayanan', $pelayanan->id_pelayanan, json_encode($validated));

            return response()->json([
                'success' => true,
                'message' => 'Data pelayanan berhasil diperbarui!',
                'data' => $pelayanan
            ]);
        }, 'Update Pelayanan');
    }

    public function destroy($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $pelayanan = Pelayanan::find($id);

            if (!$pelayanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelayanan tidak ditemukan.'
                ], 404);
            }

            $this->transactionService->handleWithLogDB('Delete Pelayanan', 'pelayanan', $id, $pelayanan);
            $pelayanan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pelayanan berhasil dihapus!'
            ]);
        }, 'Delete Pelayanan');
    }
}
