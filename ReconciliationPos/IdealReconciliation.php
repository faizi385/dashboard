<?php

use App\Traits\ICIntegrationTrait;
use Illuminate\Support\Facades\DB;
use App\Models\IdealDiagnosticReport;
use Illuminate\Support\Facades\Log;

$report = DB::table('reports')->where('pos', 'ideal')->where('status', 'pending')->first();
if($report) {
    dump($report->id . '  -- ' . date('Y-m-d H:i:s'));

    try {
        DB::beginTransaction();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'Reconciliation Start']);

        // Fetch Ideal reports where status is pending
        $idealDaignosticReport = IdealDiagnosticReport::where('report_id', $report->id)->where('status', 'pending')->get();
        dump('idealReports fetched -- ' . date('Y-m-d H:i:s'));

        $cleanSheet = [];
        $insertionCount = 1;
        $insertionLimit = 500;
        $totalReportCount = count($idealDaignosticReport);

        foreach ($idealDaignosticReport as $key => $idealDaignosticReport) {
            $cleanSheet[] = (new class {
                use ICIntegrationTrait;
            })->idealMasterCatalogue($idealDaignosticReport, $report); // Replace greenlineMasterCatalouge with idealMasterCatalogue

            $insertionCount++;

            if ($insertionCount == $insertionLimit || $key === $totalReportCount - 1) {
                DB::table('clean_sheets')->insert($cleanSheet);
                $insertionCount = 1;
                $cleanSheet = [];
            }

            if ($key === $totalReportCount - 1) {
                DB::table('ideal_diagnostic_reports')->where('report_id', $report->id)->update(['status' => 'done']);
            }
        }

        DB::table('reports')->where('id', $report->id)->update(['status' => 'Retailer Statement Process']);
        DB::commit();

    } catch (\Exception $e) {
        Log::error('Error in Ideal reconciliation: ' . $e->getMessage());
        DB::rollBack();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
    }

    print_r('ReconciliationPos process completed successfully.');
}
