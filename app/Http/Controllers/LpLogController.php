<?php

// app/Http/Controllers/LpLogController.php

namespace App\Http\Controllers;

use App\Models\LpLog;
use Illuminate\Http\Request;

class LpLogController extends Controller
{
    public function index()
    {
        $lpLogs = LpLog::with('user','lp')->orderBy('created_at', 'desc')->get();
        return view('super_admin.lp.logs.index', compact('lpLogs'));
    }
    // App\Http\Controllers\LpController.php

public function showLogs()
{
    $lpLogs = LpLog::with(['user', 'lp'])->get(); // Fetch logs with user and lp details
    return view('lp.logs', compact('lpLogs'));   // Pass to view
}

}
