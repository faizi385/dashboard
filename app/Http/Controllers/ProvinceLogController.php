<?php

namespace App\Http\Controllers;

use App\Models\ProvinceLog; // Assuming ProvinceLog is the model for province logs
use Illuminate\Http\Request;

class ProvinceLogController extends Controller
{
    public function index()
    {
        // Retrieve all province logs with the related user and province information
        $provinceLogs = ProvinceLog::with('user', 'province')->orderBy('created_at', 'desc')->get();
        $provinceLogs = ProvinceLog::with('user', 'province')->orderBy('created_at', 'desc')->paginate(10);

        // Pass the logs to the view
        return view('province_logs.index', compact('provinceLogs'));
    }


public function showLogs()
{
    $provinceLogs = ProvinceLog::with(['user', 'province'])->paginate(10);

    return view('admin.province-logs', compact('provinceLogs'));
}

}
