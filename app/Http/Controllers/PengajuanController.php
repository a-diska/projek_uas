<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\TransactionService;

class PengajuanController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        return $this->transactionService->handleWithTransaction(function () {
            $pengajuan = Pengajuan::with(['user', 'workshop', 'pelayanan'])->get();

            return response()->json([
                'success' => true,
                'message' => 'List semua pengajuan',
                'data' => $pengajuan
            ]);
        }, 'List Pengajuan');
    }

    public function create(Request $request)
    {
        $id_user = $request->id_user;

        $adaDiproses = Pengajuan::where('id_user', $id_user)
            ->where('status', 'diproses')
            ->exists();

        if ($adaDiproses) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih memiliki pengajuan dengan status diproses.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Anda boleh membuat pengajuan.'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|exists:user,id',
            'id_workshop' => 'nullable|exists:workshop,id_workshop',
            'id_pelayanan' => 'nullable|exists:pelayanan,id_pelayanan',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($validated) {
            $cekDiproses = Pengajuan::where('id_user', $validated['id_user'])
                ->where('status', 'diproses')
                ->first();

            if ($cekDiproses) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan sebelumnya masih dalam status diproses.'
                ], 403);
            }

            $pengajuan = Pengajuan::create([
                'id_user' => $validated['id_user'],
                'id_workshop' => $validated['id_workshop'] ?? null,
                'id_pelayanan' => $validated['id_pelayanan'] ?? null,
                'status' => 'diproses',
            ]);

            $this->transactionService->handleWithLogDB('Create Pengajuan', 'pengajuan', $pengajuan->id_pengajuan, json_encode($validated));

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil disimpan',
                'data' => $pengajuan
            ], 201);
        }, 'Create Pengajuan');
    }

    public function storePeserta(Request $request)
{
    $request->merge(['id_user' => $request->user()->id]);

    $request->validate([
        'dokumen.*' => 'file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:5120',
    ]);

    $response = $this->store($request);
    $data = $response->getData();

    if ($response->getStatusCode() === 201 && $request->hasFile('dokumen')) {
        $pengajuanId = $data->data->id_pengajuan;

        foreach ($request->file('dokumen') as $file) {
            $filename = now()->format('YmdHis') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('dokumen', $filename, 'public');

            Dokumen::create([
                'id_pengajuan' => $pengajuanId,
                'nama_file' => $file->getClientOriginalName(),
                'path' => 'storage/' . $path,
            ]);
        }
    }

    return $response;
}


    public function show($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $pengajuan = Pengajuan::with(['user', 'workshop', 'pelayanan', 'dokumen'])->find($id);

            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $pengajuan
            ]);
        }, 'Detail Pengajuan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'dokumen_baru.*' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:2048',
        ]);

        return $this->transactionService->handleWithTransaction(function () use ($request, $id) {
            $pengajuan = Pengajuan::with('dokumen')->findOrFail($id);

            foreach ($pengajuan->dokumen as $doc) {
                if (Storage::disk('public')->exists(str_replace('storage/', '', $doc->path))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $doc->path));
                }
                $doc->delete();
            }

            if ($request->hasFile('dokumen_baru')) {
                foreach ($request->file('dokumen_baru') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('dokumen', $filename, 'public');

                    $pengajuan->dokumen()->create([
                        'nama_file' => $file->getClientOriginalName(),
                        'path' => 'storage/' . $path,
                    ]);
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil diperbarui.',
            ]);
        }, 'update-pengajuan');
    }

    public function destroy($id)
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $pengajuan = Pengajuan::with('dokumen')->find($id);

            if (!$pengajuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan tidak ditemukan'
                ], 404);
            }

            foreach ($pengajuan->dokumen as $dokumen) {
                if ($dokumen->path) {
                    $path = str_replace('storage/', '', $dokumen->path);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
                $dokumen->delete();
            }

            $this->transactionService->handleWithLogDB('Delete Pengajuan', 'pengajuan', $id, $pengajuan);
            $pengajuan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan dan dokumen terkait berhasil dihapus.'
            ]);
        }, 'Delete Pengajuan');
    }
}
