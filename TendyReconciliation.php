<?php

namespace App\Traits;

use App\Models\TendyDiagnosticReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TendyReconciliation
{
    use TendyIntegration; // Use TendyIntegration trait for Tendy-specific methods

    /**
     * Run the reconciliation process for Tendy reports.
     */
    public function runReconciliation()
    {
        // Set the limit for reports to process
        $limit = 1; // You can adjust this limit

        // Fetch pending Tendy reports from the 'reports' table
        $reports = DB::table('reports')
            ->where('pos', 'tendy')
            ->where('status', 'pending')
            ->limit($limit)
            ->get();

        foreach ($reports as $report) {
            dump($report->id . '  -- ' . date('Y-m-d H:i:s'));

            try {
                // Mark report as started
                DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);

                // Retrieve Tendy reports related to this report
                $tendyReports = TendyDiagnosticReport::where('report_id', $report->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                              ->orWhere('status', 'error');
                    })
                    ->get();

                dump('tendyReports fetched -- ' . date('Y-m-d H:i:s'));

                // Process each Tendy report using the TendyIntegration method
                $this->processTendyReports($tendyReports);

                // Update the Tendy reports to mark them as 'done'
                DB::table('tendy_diagnostic_reports')
                    ->where('report_id', $report->id)
                    ->update(['status' => 'done']);

                // Mark the report as completed
                DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);

            } catch (\Exception $e) {
                // Log any errors encountered during processing
                Log::error('Error in Tendy reconciliation: ' . $e->getMessage());

                // Mark the report as failed if there's an error
                DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
            }
        }

        print_r('Reconciliation process completed successfully.');
    }

}

// Run the reconciliation process
$reconciliation = new TendyReconciliation();
$reconciliation->runReconciliation();
