<?php

use App\Traits\IdealIntegration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\IdealDiagnosticReport;
use App\Models\IdealSalesSummaryReport;

class IdealReconciliation
{
    use IdealIntegration; // Use the IdealIntegration trait

    /**
     * Run the reconciliation process for Ideal reports.
     */
    public function runReconciliation()
    {
        $limit = 1;

        // Fetch pending Ideal reports
        $reports = DB::table('reports')
            ->where('pos', 'ideal')
            ->where('status', 'pending')
            ->limit($limit)
            ->get();

        foreach ($reports as $report) {
            dump($report->id . ' -- ' . date('Y-m-d H:i:s'));

            try {
                DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);

                // Retrieve Ideal diagnostic reports and sales summary reports for this report ID
                $diagnosticReports = IdealDiagnosticReport::where('report_id', $report->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                              ->orWhere('status', 'error');
                    })
                    ->get();

                $salesReports = IdealSalesSummaryReport::where('report_id', $report->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                              ->orWhere('status', 'error');
                    })
                    ->get();

                // Process diagnostic reports first; if not matched, process sales reports
                $this->processIdealPOSReports($diagnosticReports, $salesReports);

                DB::table('ideal_diagnostic_reports')
                    ->where('report_id', $report->id)
                    ->update(['status' => 'done']);
                DB::table('ideal_sales_summary_reports')
                    ->where('report_id', $report->id)
                    ->update(['status' => 'done']);

                DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);

            } catch (\Exception $e) {
                Log::error('Error in Ideal reconciliation: ' . $e->getMessage());
                DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
                continue;
            }
        }
    }
}

// Run the reconciliation process
$idealReconciliation = new IdealReconciliation();
$idealReconciliation->runReconciliation();

print_r('Reconciliation process completed successfully.');
