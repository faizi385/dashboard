<?php
use App\Traits\ICIntegrationTrait;
use Illuminate\Support\Facades\DB;
use App\Models\GreenlineReport;
use Illuminate\Support\Facades\Log;


$report = DB::table('reports')->where('pos', 'greenline')->where('status', 'pending')->first();
dump($report->id . '  -- ' . date('Y-m-d H:i:s'));
try {
    DB::beginTransaction();
    DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);
    $greenlineReports = GreenlineReport::where('report_id', $report->id)->where('status', 'pending')->get();
    dump('greenlineReports fetched -- ' . date('Y-m-d H:i:s'));
    $cleanSheet = [];  $insertionCount = 1; $insertionLimit = 500; $totalReportCount = count($greenlineReports);
    foreach ($greenlineReports as $key => $greenlineReport){
        $cleanSheet[] = (new class {
            use ICIntegrationTrait;
        })->greenlineMasterCatalouge($greenlineReport, $report);
        $insertionCount++;
        if($insertionCount == $insertionLimit || $key === $totalReportCount - 1){
            DB::table('clean_sheets')->insert($cleanSheet);
            $insertionCount = 1;
            $cleanSheet = [];
        }
        if($key === $totalReportCount - 1){
            DB::table('greenline_reports')->where('report_id',$report->id)->update(['status'=>'done']);
        }
    }
    DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);
    DB::commit();
} catch (\Exception $e) {
    Log::error('Error in Greenline reconciliation: ' . $e->getMessage());
    DB::rollBack();
    DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);

}


print_r('Reconciliation process completed successfully.');
