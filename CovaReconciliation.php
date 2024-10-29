<?php

use App\Traits\CovaICIntegration;
use App\Traits\ICIntegrationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CovaDiagnosticReport;
use App\Models\CovaSalesReport;

$limit = 1;
$reports = RetailerReportSubmission::where('pos', 'cova')->where('status', 'reconciliation_process')->limit($limit)->get();

foreach ($reports as $retailerReportSubmission) {
    try {
        $insertIntoLogs = DB::table('cron_logs')->insert([
            'start_time' => now(),
            'retailerReportSubmission_id' => $retailerReportSubmission->id
        ]);
        DB::table('retailer_report_submissions')->where('id', $retailerReportSubmission->id)->update(['status' => 'reconciliation_start']);
        DB::beginTransaction();
        $retailer_id = $retailerReportSubmission->retailer_id;
        $covaDaignosticReports =  CovaDiagnosticReport::with('CovaSalesSummaryReport')->where('retailerReportSubmission_id', $retailerReportSubmission->id)->where('entry_status', 'pending')->orWhere('entry_status', 'error')->get();
       dump('covaDaignosticReports fetch -- '.date('Y-m-d H:i:s'));
        $data = []; $cleanSheet = [];  $insertionCount = 1; $insertionLimit = 500; $totalReportCount = count($covaDaignosticReports);
        foreach ($covaDaignosticReports as $key => $covaDaignosticReport) {
            $cleanSheet[] = (new class
            {
                use ICIntegrationTrait;
            })->covaMasterCatalouge($covaDaignosticReport, $retailerReportSubmission);
            $insertionCount++;
            if($insertionCount == $insertionLimit || $key === $totalReportCount - 1){
               dump('before insertion  -- '.date('Y-m-d H:i:s'));
                DB::table('clean_sheets')->insert($cleanSheet);
                $insertionCount = 1;
                $cleanSheet = [];
            }
            if($key === $totalReportCount - 1){
                DB::table('cova_diagnostic_reports')->where('retailerReportSubmission_id',$retailerReportSubmission->id)->update(['entry_status'=>'done']);
            }
        }
        DB::table('retailer_report_submissions')->where('id', $retailerReportSubmission->id)->update(['status' => 'retailer_statement_process']);

        $insertIntoLogs = DB::table('cron_logs')->where('retailerReportSubmission_id', $retailerReportSubmission->id)->update([
            'end_time' => now()
        ]);
        DB::commit();
    } catch (\Exception $e) {
        Log::error('Error occurred IN Cova Cron Reconciliation--: ' . $e);
        DB::rollback();
        DB::table('retailer_report_submissions')->where('id', $retailerReportSubmission->id)->update([
            'status' => 'failed'
        ]);
        DB::table('cron_logs')->where('retailerReportSubmission_id', $retailerReportSubmission->id)->update([
            'end_time' => now(),
            'error_log' => $e->getMessage()
        ]);
        continue;
    }
}

class CovaReconciliation
{
    use CovaICIntegration; // Use the Cova trait

    /**
     * Run the reconciliation process for GlobalTill reports.
     */
    public function runReconciliation()
    {
        $limit = 1;

        // Fetch pending GlobalTill reports
        $reports = DB::table('reports')
            ->where('pos', 'cova')
            ->where('status', 'pending')
            ->limit($limit)
            ->get();

        foreach ($reports as $report) {
            try {
                DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);
                $diagnosticReports = CovaDiagnosticReport::where('report_id', $report->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                            ->orWhere('status', 'error');
                    })
                    ->get();

                $salesReports = CovaSalesReport::where('report_id', $report->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                            ->orWhere('status', 'error');
                    })
                    ->get();

                $cleanSheet[] = (new class
                {
                    use ICIntegrationTrait;
                })->covaMasterCatalouge($diagnosticReports, $reports);
//                $this->processCovaReports($diagnosticReports,$reports);

                DB::table('global_till_diagnostic_reports')
                    ->where('report_id', $report->id)
                    ->update(['status' => 'done']);
                DB::table('globaltill_sales_summary_reports')
                    ->where('report_id', $report->id)
                    ->update(['status' => 'done']);

                DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);

            } catch (\Exception $e) {
                Log::error('Error in GlobalTill reconciliation: ' . $e->getMessage());
                DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
                continue;
            }
        }
    }
}

// Run the reconciliation process
$covaReconciliation = new CovaReconciliation();
$covaReconciliation->runReconciliation();

print_r('Reconciliation process completed successfully.');
