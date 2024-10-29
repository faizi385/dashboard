<?php

use App\Traits\GlobalTillIntegration; // Use the GlobalTillIntegration trait
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\GlobalTillDiagnosticReport;
use App\Models\GlobalTillSalesSummaryReport;

class GlobalTillReconciliation
{
    use GlobalTillIntegration; // Use the GlobalTillIntegration trait

    /**
     * Run the reconciliation process for GlobalTill reports.
     */
    public function runReconciliation()
    {
        $limit = 1;

        // Fetch pending GlobalTill reports
        $reports = DB::table('reports')
            ->where('pos', 'global')
            ->where('status', 'pending')
            ->limit($limit)
            ->get();

        foreach ($reports as $report) {
            dump($report->id . ' -- ' . date('Y-m-d H:i:s'));

            try {
                DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);

                // Retrieve GlobalTill diagnostic reports and sales summary reports for this report ID
                $diagnosticReports = GlobalTillDiagnosticReport::where('report_id', $report->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                              ->orWhere('status', 'error');
                    })
                    ->get();

                $salesReports = GlobalTillSalesSummaryReport::where('report_id', $report->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                              ->orWhere('status', 'error');
                    })
                    ->get();

                // Process diagnostic reports first; if not matched, process sales reports
                $this->processGlobalTillReports($diagnosticReports, $salesReports);

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
$globalTillReconciliation = new GlobalTillReconciliation();
$globalTillReconciliation->runReconciliation();

print_r('Reconciliation process completed successfully.');
