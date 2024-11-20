<?php
use App\Traits\ICIntegrationTrait;
use Illuminate\Support\Facades\DB;
use App\Models\TechPOSReport;
use Illuminate\Support\Facades\Log;


$report = DB::table('reports')->where('pos', 'techpos')->where('status', 'pending')->first();
if($report) {
    dump($report->id . '  -- ' . date('Y-m-d H:i:s'));
    try {
        DB::beginTransaction();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);
        $techPOSReports = TechPosReport::where('report_id', $report->id)->where('status', 'pending')->get();
        dump('techPOSReports fetched -- ' . date('Y-m-d H:i:s'));
        $cleanSheet = [];
        $insertionCount = 1;
        $insertionLimit = 500;
        $totalReportCount = count($techPOSReports);
        foreach ($techPOSReports as $key => $techPOSReport) {
            $cleanSheet[] = (new class {
                use ICIntegrationTrait;
            })->techPOSMasterCatalouge($techPOSReport, $report);
            $insertionCount++;
            if ($insertionCount == $insertionLimit || $key === $totalReportCount - 1) {
                DB::table('clean_sheets')->insert($cleanSheet);
                $insertionCount = 1;
                $cleanSheet = [];
            }
            if ($key === $totalReportCount - 1) {
                DB::table('tech_pos_reports')->where('report_id', $report->id)->update(['status' => 'done']);
            }
        }
        DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);
        DB::commit();
    } catch (\Exception $e) {
        Log::error('Error in TechPOS reconciliation: ' . $e->getMessage());
        DB::rollBack();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);

    }


    print_r('ReconciliationPos process completed successfully.');
}

