<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        return $this->transactionService->handleWithTransaction(function () {
            $dokumen = Dokumen::with('pengajuan')->get();

            return response()->json([
                'success' => true,
                'message' => 'List semua dokumen',
                'data' => $dokumen
            ]);
        }, 'List Dokumen');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pengajuan' => 'required|exists:pengajuan,id_pengajuan',
            'dokumen.*' => 'required|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:2048'
        ], [
            'id_pengajuan.required' => 'ID pengajuan wajib diisi.',
            'id_pengajuan.exists' => 'Pengajuan tidak ditemukan.',
            'dokumen.*.required' => 'File dokumen wajib diisi.',
            'dokumen.*.file' => 'File tidak valid.',
            'dokumen.*.mimes' => 'Format file harus PDF, DOC, DOCX, TXT, JPG, JPEG, PNG.',
            'dokumen.*.max' => 'Ukuran maksimal file 2MB.'
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $dokumenData = [];

            foreach ($request->file('dokumen') as $file) {
                $filename = now()->format('YmdHis') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('dokumen', $filename, 'public'); 

                $dokumen = Dokumen::create([
                    'id_pengajuan' => $request->id_pengajuan,
                    'nama_file' => $filename,
                    'path' => 'storage/' . $path, 
                ]);

                $dokumenData[] = $dokumen;
            }

            $this->transactionService->handleWithLogDB('store-dokumen', 'dokumen', $request->id_pengajuan, json_encode($dokumenData));

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diunggah.',
                'data' => $dokumenData
            ], 201);
        }, 'store-dokumen');
    }

    public function show($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $dokumen = Dokumen::with('pengajuan')->find($id);

            if (!$dokumen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail dokumen',
                'data' => $dokumen
            ]);
        }, 'Detail Dokumen');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_pengajuan' => 'nullable|exists:pengajuan,id_pengajuan',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:2048'
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request, $id) {
            $dokumen = Dokumen::find($id);

            if (!$dokumen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan.'
                ], 404);
            }

            if ($request->hasFile('file')) {
                if ($dokumen->path) {
                    $oldPath = str_replace('storage/', '', $dokumen->path);
                    Storage::disk('public')->delete($oldPath);
                }

                $file = $request->file('file');
                $filename = now()->format('YmdHis') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('dokumen', $filename, 'public');

                $dokumen->nama_file = $filename;
                $dokumen->path = 'storage/' . $path;
            }

            $dokumen->id_pengajuan = $request->id_pengajuan ?? $dokumen->id_pengajuan;
            $dokumen->save();

            $this->transactionService->handleWithLogDB('Update Dokumen', 'dokumen', $dokumen->id_dokumen, json_encode($dokumen));

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diperbarui!',
                'data' => $dokumen
            ]);
        }, 'Update Dokumen');
    }

    public function destroy($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $dokumen = Dokumen::find($id);

            if (!$dokumen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan.'
                ], 404);
            }

            if ($dokumen->path) {
                $storagePath = str_replace('storage/', '', $dokumen->path);
                Storage::disk('public')->delete($storagePath);
            }

            $this->transactionService->handleWithLogDB('Delete Dokumen', 'dokumen', $dokumen->id_dokumen, json_encode($dokumen));
            $dokumen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus!'
            ]);
        }, 'Delete Dokumen');
    }
}
