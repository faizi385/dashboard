<?php

use App\Traits\ICIntegrationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProfitTechInventoryLog;


$report = DB::table('reports')->where('pos', 'profittech')->where('status', 'pending')->first();

if ($report) {
    dump($report->id . '  -- ' . date('Y-m-d H:i:s'));

    try {
        DB::beginTransaction();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);

        $profitTechReports = ProfitTechInventoryLog::where('report_id', $report->id)->where('status', 'pending')->get();
        dump('profitTechReports fetched -- ' . date('Y-m-d H:i:s'));

        $cleanSheet = [];
        $insertionCount = 1;
        $insertionLimit = 500;
        $totalReportCount = count($profitTechReports);

        foreach ($profitTechReports as $key => $profitTechReport) {
            $cleanSheet[] = (new class {
                use ICIntegrationTrait;
            })->profitTechMasterCatalouge($profitTechReport, $report); // Ensure profitTechMasterCatalog exists in the trait

            $insertionCount++;

            if ($insertionCount == $insertionLimit || $key === $totalReportCount - 1) {
                DB::table('clean_sheets')->insert($cleanSheet);
                $insertionCount = 1;
                $cleanSheet = [];
            }

            if ($key === $totalReportCount - 1) {
                DB::table('profittech_pos_reports')->where('report_id', $report->id)->update(['status' => 'done']);
            }
        }

        DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);
        DB::commit();
    } catch (\Exception $e) {
        Log::error('Error in ProfitTech reconciliation: ' . $e->getMessage());
        DB::rollBack();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
    }

    print_r('ReconciliationPos process completed successfully.');
}
