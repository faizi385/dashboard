<?php
namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = Log::with('actionUser', 'user')->orderBy('created_at', 'desc')->get();
        return view('logs.index', compact('logs'));
    }
    
    public function show($id)
{
    $log = Log::find($id);

    if (!$log) {
        return response()->json(['error' => 'Log not found'], 404);
    }

    return response()->json(['description' => $log->description]);
}

}
