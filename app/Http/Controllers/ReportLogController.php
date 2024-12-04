<?php
namespace App\Http\Controllers;

use App\Models\ReportLog;
use Illuminate\Http\Request;

class ReportLogController extends Controller
{
    // Display a listing of the report logs
    public function index()
    {
        // Fetch the report logs from the database
        $reportLogs = ReportLog::with('report.retailer', 'user')->latest()->get();
    
        // dd($reportLogs);
        return view('reports.report_logs.index', compact('reportLogs'));
    }
 
}
