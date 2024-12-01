<?php

use App\Models\BarnetPosReport;
use App\Models\BarnetInventoryLog;
use App\Traits\ICIntegrationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

$report = DB::table('reports')->where('pos', 'barnet')->where('status', 'pending')->first();
if ($report) {
    dump($report->id . '  -- ' . date('Y-m-d H:i:s'));
    try {
        DB::beginTransaction();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'Reconciliation Start']);
        $barnetReports = BarnetPosReport::where('report_id', $report->id)->where('status', 'pending')->get();
        dump('BarnetReports fetched -- ' . date('Y-m-d H:i:s'));
        $cleanSheet = [];
        $insertionCount = 1;
        $insertionLimit = 500;
        $totalReportCount = count($barnetReports);
        foreach ($barnetReports as $key => $barnetReport) {
            $cleanSheet[] = (new class {
                use ICIntegrationTrait;
            })->barnetMasterCatalog($barnetReport, $report); // Ensure barnetMasterCatalog exists in the trait
            $insertionCount++;
            if ($insertionCount == $insertionLimit || $key === $totalReportCount - 1) {
                DB::table('clean_sheets')->insert($cleanSheet);
                $insertionCount = 1;
                $cleanSheet = [];
            }
            if ($key === $totalReportCount - 1) {
                DB::table('barnet_pos_reports')->where('report_id', $report->id)->update(['status' => 'done']);
            }
        }
        DB::table('reports')->where('id', $report->id)->update(['status' => 'Retailer Statement Process']);
        DB::commit();
    } catch (\Exception $e) {
        Log::error('Error in Barnet reconciliation: ' . $e->getMessage());
        DB::rollBack();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
    }
    print_r('ReconciliationPos process completed successfully.');
} else {
    print_r('No pending Barnet reports found.');
}
