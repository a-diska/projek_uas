<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengajuan;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'totalAdmin' => User::where('id_role', 1)->count(),
            'totalPeserta' => User::where('id_role', 3)->count(),
            'totalVerifikator' => User::where('id_role', 2)->count(),
            'totalPengajuan' => Pengajuan::count(),
        ]);
    }
}
