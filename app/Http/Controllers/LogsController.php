<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;
use App\Models\LogDatabase;
use App\Models\LogError;
use App\Models\LogApproval;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function activity(Request $request)
    {
        $logs = LogActivity::orderBy('tanggal', 'desc')->paginate(20);
        return response()->json($logs); 
    }

    public function database(Request $request)
    {
        $logs = LogDatabase::orderBy('tanggal', 'desc')->paginate(20);
        return response()->json($logs);
    }

    public function error(Request $request)
    {
        $logs = LogError::orderBy('tanggal', 'desc')->paginate(10);
        return response()->json($logs);
    }

    public function approval(Request $request)
    {
        $logs = LogApproval::with(['pengajuan', 'verifikator'])->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($logs);
    }
}
