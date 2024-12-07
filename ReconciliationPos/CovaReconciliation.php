<?php

use App\Traits\ICIntegrationTrait;
use Illuminate\Support\Facades\DB;
use App\Models\CovaDiagnosticReport;
use Illuminate\Support\Facades\Log;

$limit = 1;
$report = DB::table('reports')->where('pos', 'cova')->where('status', 'pending')->first();
if($report) {
    dump($report->id . '  -- ' . date('Y-m-d H:i:s'));
    try {
        DB::table('reports')->where('id', $report->id)->update(['status' => 'Reconciliation Start']);
        DB::beginTransaction();
        $covaDaignosticReports = CovaDiagnosticReport::with('CovaSalesSummaryReport')->where('report_id', $report->id)->where('status', 'pending')->get();
        $data = [];
        $cleanSheet = [];
        $insertionCount = 1;
        $insertionLimit = 500;
        $totalReportCount = count($covaDaignosticReports);
        foreach ($covaDaignosticReports as $key => $covaDaignosticReport) {
            $cleanSheet[] = (new class {
                use ICIntegrationTrait;
            })->covaMasterCatalouge($covaDaignosticReport, $report);
            $insertionCount++;
            if ($insertionCount == $insertionLimit || $key === $totalReportCount - 1) {
                dump('before insertion  -- ' . date('Y-m-d H:i:s'));
                DB::table('clean_sheets')->insert($cleanSheet);
                $insertionCount = 1;
                $cleanSheet = [];
            }
            if ($key === $totalReportCount - 1) {
                DB::table('cova_diagnostic_reports')->where('report_id', $report->id)->update(['status' => 'done']);
            }
        }
        DB::table('reports')->where('id', $report->id)->update(['status' => 'Retailer Statement Process']);
        DB::commit();
    } catch (\Exception $e) {
        Log::error('Error in Cova reconciliation: ' . $e->getMessage());
        DB::rollBack();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
    }
}
