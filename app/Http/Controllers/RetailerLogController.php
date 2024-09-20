<?php
namespace App\Http\Controllers;

use App\Models\RetailerLog;
use Illuminate\Http\Request;

class RetailerLogController extends Controller
{
    public function index()
    {
        $retailerLogs = RetailerLog::with('user', 'retailer')->get();
        return view('retailer.logs', compact('retailerLogs'));
    }
}
