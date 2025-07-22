<?php

namespace App\Http\Controllers;

use App\Models\LogApproval;
use App\Models\Pengajuan;
use App\Models\Verifikator;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class ApprovalPengajuanController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $user = $request->user();

            if (!$user || !$user->role || $user->role->nama_role !== 'verifikator') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses sebagai verifikator.'
                ], 403);
            }

            $verifikator = Verifikator::where('id_user', $user->id)->first();
            if (!$verifikator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data verifikator tidak ditemukan.'
                ], 403);
            }

            $query = Pengajuan::with(['user', 'pelayanan', 'workshop', 'dokumen', 'logApproval']);

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $data = $query->orderByDesc('created_at')->get();

            return response()->json([
                'success' => true,
                'message' => $request->has('status')
                    ? 'Pengajuan dengan status ' . $request->status
                    : 'Semua data pengajuan',
                'data' => $data
            ]);
        }, 'verifikator-list-pengajuan');
    }

    public function approve(Request $request, $id_pengajuan)
    {
        return $this->transactionService->handleWithTransaction(function () use ($request, $id_pengajuan) {
            $request->validate([
                'status' => 'required|in:disetujui,ditolak',
                'catatan' => 'nullable|string|max:1000',
            ]);

            $user = $request->user();
            $verifikator = Verifikator::where('id_user', $user->id)->first();

            if (!$verifikator) {
                return response()->json(['success' => false, 'message' => 'Kamu bukan verifikator.'], 403);
            }

            $pengajuan = Pengajuan::find($id_pengajuan);
            if (!$pengajuan) {
                return response()->json(['success' => false, 'message' => 'Pengajuan tidak ditemukan.'], 404);
            }

            if (in_array($pengajuan->status, ['ditolak', 'disetujui'])) {
                return response()->json(['success' => false, 'message' => 'Pengajuan sudah ' . strtoupper($pengajuan->status) . '. Tidak dapat diubah.'], 400);
            }

            $sudahApprove = LogApproval::where('id_pengajuan', $id_pengajuan)
                ->where('id_verifikator', $verifikator->id_verifikator)
                ->exists();

            if ($sudahApprove) {
                return response()->json(['success' => false, 'message' => 'Anda sudah memberikan keputusan.'], 400);
            }

            $logSaatIni = LogApproval::where('id_pengajuan', $id_pengajuan)->count();
            if ($logSaatIni + 1 !== $verifikator->tahapan) {
                return response()->json(['success' => false, 'message' => 'Belum giliran Anda untuk verifikasi.'], 403);
            }

            LogApproval::create([
                'id_pengajuan' => $id_pengajuan,
                'id_verifikator' => $verifikator->id_verifikator,
                'status' => $request->status,
                'catatan' => $request->catatan
            ]);

            if ($request->status === 'ditolak') {
            $pengajuan->update([
                'status'  => 'ditolak',
                'catatan' => $request->catatan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil ditolak.'
            ], 200);
        }

            $totalVerifikator = Verifikator::count();
            $totalDisetujui = LogApproval::where('id_pengajuan', $id_pengajuan)
                ->where('status', 'disetujui')->count();
            $adaYangMenolak = LogApproval::where('id_pengajuan', $id_pengajuan)
                ->where('status', 'ditolak')->exists();

            if ($totalDisetujui === $totalVerifikator && !$adaYangMenolak) {
                $pengajuan->update(['status' => 'disetujui']);
                return response()->json(['success' => true, 'message' => 'Pengajuan berhasil disetujui.']);
            }

            return response()->json(['success' => true, 'message' => 'Keputusan Anda berhasil dicatat. Menunggu verifikator selanjutnya.']);
        }, 'approve-verifikator-pengajuan');
    }
}
