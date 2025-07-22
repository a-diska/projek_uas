<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use App\Services\TransactionService;

class WorkshopController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        return $this->transactionService->handleWithTransaction(function () {
            $workshops = Workshop::all();

            return response()->json([
                'success' => true,
                'message' => 'List semua workshop',
                'data' => $workshops
            ]);
        }, 'List Workshop');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_workshop' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'required|string|max:150'
        ], [
            'nama_workshop.required' => 'Nama workshop wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'lokasi.required' => 'Lokasi wajib diisi.'
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($validated) {
            $workshop = Workshop::create($validated);

            $this->transactionService->handleWithLogDB('Create Workshop', 'workshop', $workshop->id_workshop, json_encode($validated));

            return response()->json([
                'success' => true,
                'message' => 'Workshop berhasil ditambahkan!',
                'data' => $workshop
            ], 201);
        }, 'Create Workshop');
    }

    public function show($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $workshop = Workshop::find($id);

            if (!$workshop) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workshop tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail workshop',
                'data' => $workshop
            ]);
        }, 'Detail Workshop');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_workshop' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'required|string|max:150'
        ], [
            'nama_workshop.required' => 'Nama workshop wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'lokasi.required' => 'Lokasi wajib diisi.'
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($id, $validated) {
            $workshop = Workshop::find($id);

            if (!$workshop) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workshop tidak ditemukan.'
                ], 404);
            }

            $workshop->update($validated);

            $this->transactionService->handleWithLogDB('Update Workshop', 'workshop', $workshop->id_workshop, json_encode($validated));

            return response()->json([
                'success' => true,
                'message' => 'Data workshop berhasil diperbarui!',
                'data' => $workshop
            ]);
        }, 'Update Workshop');
    }

    public function destroy($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $workshop = Workshop::find($id);

            if (!$workshop) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workshop tidak ditemukan.'
                ], 404);
            }

            $this->transactionService->handleWithLogDB('Delete Workshop', 'workshop', $id, $workshop);
            $workshop->delete();

            return response()->json([
                'success' => true,
                'message' => 'Workshop berhasil dihapus!'
            ]);
        }, 'Delete Workshop');
    }
}
