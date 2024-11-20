<?php

use App\Traits\ICIntegrationTrait;
use Illuminate\Support\Facades\DB;
use App\Models\GlobalTillDiagnosticReport;
use Illuminate\Support\Facades\Log;

$report = DB::table('reports')->where('pos', 'global')->where('status', 'pending')->first();

if (!$report) {
    Log::info('No pending GlobalTill reports found.');
    exit;
}

dump($report->id . ' -- ' . date('Y-m-d H:i:s'));

try {
    DB::beginTransaction();
    DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);
    
    // Fetch GlobalTill diagnostic reports where status is pending
    $globalTillDiagnosticReports = GlobalTillDiagnosticReport::where('report_id', $report->id)->where('status', 'pending')->get();
    dump('GlobalTill reports fetched -- ' . date('Y-m-d H:i:s'));
    
    if ($globalTillDiagnosticReports->isEmpty()) {
        throw new \Exception("No pending GlobalTill Diagnostic Reports found for report ID {$report->id}");
    }

    $cleanSheet = [];  
    $insertionCount = 1; 
    $insertionLimit = 500; 
    $totalReportCount = count($globalTillDiagnosticReports);
    
    foreach ($globalTillDiagnosticReports as $key => $globalTillDiagnosticReport) {
        $cleanSheet[] = (new class {
            use ICIntegrationTrait;
        })->globaltillMasterCatalogue($globalTillDiagnosticReport, $report); // Ensure method is correctly implemented
        
        $insertionCount++;
        
        if ($insertionCount == $insertionLimit || $key === $totalReportCount - 1) {
            DB::table('clean_sheets')->insert($cleanSheet);
            $insertionCount = 1;
            $cleanSheet = [];
        }
        
        if ($key === $totalReportCount - 1) {
            DB::table('global_till_diagnostic_reports')->where('report_id', $report->id)->update(['status' => 'done']);
        }
    }
    
    DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer statement process']);
    DB::commit();
    
} catch (\Exception $e) {
    Log::error('Error in GlobalTill reconciliation: ' . $e->getMessage());
    DB::rollBack();
    DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
}

print_r('GlobalTill reconciliation process completed successfully.');
