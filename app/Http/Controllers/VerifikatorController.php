<?php

namespace App\Http\Controllers;

use App\Models\Verifikator;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerifikatorController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        return $this->transactionService->handleWithTransaction(function () {
            $verifikator = Verifikator::with('user')->get();

            return response()->json([
                'success' => true,
                'data' => $verifikator
            ]);
        }, 'List Verifikator');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user'  => 'nullable|exists:user,id',
            'tahapan'  => 'nullable|integer|min:1',
            'jabatan'  => 'nullable|string|max:255',
            'status'   => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        return $this->transactionService->handleWithTransaction(function () use ($validator, $request) {
            $verifikator = Verifikator::create($validator->validated());

            $this->transactionService->handleWithLogDB(
                'Create Verifikator',
                'verifikator',
                $verifikator->id_verifikator,
                json_encode($verifikator)
            );

            return response()->json([
                'success' => true,
                'message' => 'Verifikator berhasil ditambahkan',
                'data' => $verifikator
            ]);
        }, 'Create Verifikator');
    }

    public function show($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $verifikator = Verifikator::with('user')->where('id_verifikator', $id)->first();

            if (!$verifikator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikator tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $verifikator
            ]);
        }, 'Detail Verifikator');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_user'  => 'nullable|exists:user,id',
            'tahapan'  => 'nullable|integer|min:1',
            'jabatan'  => 'nullable|string|max:255',
            'status'   => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        return $this->transactionService->handleWithTransaction(function () use ($request, $validator, $id) {
            $verifikator = Verifikator::where('id_verifikator', $id)->first();

            if (!$verifikator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikator tidak ditemukan'
                ], 404);
            }

            $verifikator->update($validator->validated());

            $this->transactionService->handleWithLogDB(
                'Update Verifikator',
                'verifikator',
                $verifikator->id_verifikator,
                json_encode($verifikator)
            );

            return response()->json([
                'success' => true,
                'message' => 'Verifikator berhasil diperbarui',
                'data' => $verifikator
            ]);
        }, 'Update Verifikator');
    }

    public function destroy($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $verifikator = Verifikator::where('id_verifikator', $id)->first();

            if (!$verifikator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikator tidak ditemukan'
                ], 404);
            }

            $this->transactionService->handleWithLogDB(
                'Delete Verifikator',
                'verifikator',
                $verifikator->id_verifikator,
                json_encode($verifikator)
            );

            $verifikator->delete();

            return response()->json([
                'success' => true,
                'message' => 'Verifikator berhasil dihapus'
            ]);
        }, 'Delete Verifikator');
    }
}
