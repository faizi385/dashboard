<?php
// namespace App\Traits\Greenline;
namespace App\Traits;

use App\Traits\GreenlineICIntegration;
use Illuminate\Support\Facades\DB;
use App\Models\GreenlineReport;
use Illuminate\Support\Facades\Log;

class GreenLineReconciliation
{
    use GreenlineICIntegration;

    /**
     * Run the reconciliation process for Greenline reports.
     */
    public function runReconciliation()
    {
        $report = DB::table('reports')->where('pos', 'greenline')->where('status', 'pending')->first();
        dump($report->id . '  -- ' . date('Y-m-d H:i:s'));
        try {
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
                    DB::table('greenline_reports')->where('report_id',$report->id)->update(['entry_status'=>'done']);
                }
            }
            DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);
        } catch (\Exception $e) {
            Log::error('Error in Greenline reconciliation: ' . $e->getMessage());
            DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);

        }
    }
}

$reconciliation = new GreenLineReconciliation();
$reconciliation->runReconciliation();

print_r('Reconciliation process completed successfully.');
