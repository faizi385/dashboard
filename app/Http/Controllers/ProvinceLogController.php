<?php

namespace App\Http\Controllers;

use App\Models\ProvinceLog;
use Illuminate\Http\Request;

class ProvinceLogController extends Controller
{
    public function index()
    {
        
        $provinceLogs = ProvinceLog::with('user', 'province')->orderBy('created_at', 'desc')->get();
        $provinceLogs = ProvinceLog::with('user', 'province')->orderBy('created_at', 'desc')->paginate(10);

       
        return view('super_admin.provinces.province_logs.index', compact('provinceLogs'));
    }


public function showLogs()
{
    $provinceLogs = ProvinceLog::with(['user', 'province'])->paginate(10);

    return view('admin.province-logs', compact('provinceLogs'));
}

}
