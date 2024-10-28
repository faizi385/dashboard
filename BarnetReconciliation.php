<?php

namespace App\Reconciliations;

use App\Traits\BarnetIntegration; // Ensure this trait exists
use Illuminate\Support\Facades\DB;
use App\Models\BarnetPosReport; // Ensure you have this model set up
use Illuminate\Support\Facades\Log;

class BarnetReconciliation
{
    use BarnetIntegration; // Use the BarnetIntegration trait

    /**
     * Run the reconciliation process for Barnet reports.
     */
    public function runReconciliation()
    {
        // Set the limit for reports to process
        $limit = 1; // You can adjust this limit

        // Fetch pending Barnet reports from the 'reports' table
        $reports = DB::table('reports')
            ->where('pos', 'barnet')
            ->where('status', 'pending')
            ->limit($limit)
            ->get();

        foreach ($reports as $report) {
            dump($report->id . '  -- ' . date('Y-m-d H:i:s'));

            try {
                // Mark report as started
                DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);

                // Retrieve Barnet reports related to this report
                $barnetReports = BarnetPosReport::where('report_id', $report->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                              ->orWhere('status', 'error');
                    })
                    ->get();

                dump('Barnet reports fetched -- ' . date('Y-m-d H:i:s'));

                // Process each Barnet report using the BarnetIntegration method
                $this->processBarnetReports($barnetReports);

                // Update the Barnet reports to mark them as 'done'
                DB::table('barnet_pos_reports')
                    ->where('report_id', $report->id)
                    ->update(['status' => 'done']);

                // Mark the report as completed
                DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);

            } catch (\Exception $e) {
                // Log any errors encountered during processing
                Log::error('Error in Barnet reconciliation: ' . $e->getMessage());

                // Mark the report as failed if there's an error
                DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);

                continue; // Move on to the next report
            }
        }
    }
}

// Run the reconciliation process
$barnetReconciliation = new BarnetReconciliation();
$barnetReconciliation->runReconciliation();

print_r('Barnet reconciliation process completed successfully.');
