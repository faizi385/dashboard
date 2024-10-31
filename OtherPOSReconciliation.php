<?php

use App\Models\OtherPOSReport;
use App\Traits\ICIntegrationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

$report = DB::table('reports')->where('pos', 'otherpos')->where('status', 'pending')->first();

if ($report) {
    dump($report->id . '  -- ' . date('Y-m-d H:i:s'));

    try {
        DB::beginTransaction();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);
        
        $otherPOSReports = OtherPOSReport::where('report_id', $report->id)->where('status', 'pending')->get();
        dump('OtherPOS reports fetched -- ' . date('Y-m-d H:i:s'));

        $cleanSheet = [];
        $insertionCount = 1;
        $insertionLimit = 500;
        $totalReportCount = count($otherPOSReports);

        foreach ($otherPOSReports as $key => $otherPOSReport) {
            $cleanSheet[] = (new class {
                use ICIntegrationTrait;
            })->otherPOSMasterCatalog($otherPOSReport, $report); // Ensure otherPOSMasterCatalog exists in the trait

            $insertionCount++;
            
            if ($insertionCount == $insertionLimit || $key === $totalReportCount - 1) {
                DB::table('clean_sheets')->insert($cleanSheet);
                $insertionCount = 1;
                $cleanSheet = [];
            }

            if ($key === $totalReportCount - 1) {
                DB::table('other_pos_reports')->where('report_id', $report->id)->update(['status' => 'done']);
            }
        }

        DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);
        DB::commit();
    } catch (\Exception $e) {
        Log::error('Error in OtherPOS reconciliation: ' . $e->getMessage());
        DB::rollBack();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
    }

    print_r('Reconciliation process completed successfully.');
} else {
    print_r('No pending OtherPOS reports found.');
}
