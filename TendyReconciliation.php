<?php

use App\Models\TendyDiagnosticReport;
use App\Traits\ICIntegrationTrait;
use Illuminate\Support\Facades\DB;
use App\Models\TendyReport;
use Illuminate\Support\Facades\Log;

$report = DB::table('reports')->where('pos', 'tendy')->where('status', 'pending')->first();

if ($report) {
    dump($report->id . '  -- ' . date('Y-m-d H:i:s'));

    try {
        DB::beginTransaction();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);
        
        $tendyDaignosticReport = TendyDiagnosticReport::where('report_id', $report->id)->where('status', 'pending')->get();
        dump('Tendy reports fetched -- ' . date('Y-m-d H:i:s'));

        $cleanSheet = [];
        $insertionCount = 1;
        $insertionLimit = 500;
        $totalReportCount = count($tendyDaignosticReport);

        foreach ($tendyDaignosticReport as $key => $tendyDaignosticReport) {
            $cleanSheet[] = (new class {
                use ICIntegrationTrait;
            })->tendyMasterCatalog($tendyDaignosticReport,$report); // Ensure tendyMasterCatalog exists in the trait

            $insertionCount++;
            
            if ($insertionCount == $insertionLimit || $key === $totalReportCount - 1) {
                DB::table('clean_sheets')->insert($cleanSheet);
                $insertionCount = 1;
                $cleanSheet = [];
            }

            if ($key === $totalReportCount - 1) {
                DB::table('tendy_diagnostic_reports')->where('report_id', $report->id)->update(['status' => 'done']);
            }
        }

        DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);
        DB::commit();
    } catch (\Exception $e) {
        Log::error('Error in Tendy reconciliation: ' . $e->getMessage());
        DB::rollBack();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
    }

    print_r('Reconciliation process completed successfully.');
} else {
    print_r('No pending Tendy reports found.');
}
