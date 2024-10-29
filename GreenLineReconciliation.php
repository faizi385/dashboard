<?php
// namespace App\Traits\Greenline;
namespace App\Traits;

use App\Traits\GreenlineICIntegration;
use Illuminate\Support\Facades\DB;
use App\Models\GreenlineReport;
use Illuminate\Support\Facades\Log;

class GreenLineReconciliation
{
    use GreenlineICIntegration; // If this is still needed for methods related to Greenline integration

    /**
     * Run the reconciliation process for Greenline reports.
     */
    public function runReconciliation()
    {
        // Set the limit for reports to process
        $limit = 1; // You can adjust this limit

        // Fetch pending Greenline reports from the 'reports' table
        $reports = DB::table('reports')
            ->where('pos', 'greenline')
            ->where('status', 'pending')
            ->limit($limit)
            ->get();

        foreach ($reports as $report) {
            dump($report->id . '  -- ' . date('Y-m-d H:i:s'));

            try {
                // Mark report as started
                DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);

                // Retrieve Greenline reports related to this report
                $greenlineReports = GreenlineReport::where('report_id', $report->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                              ->orWhere('status', 'error');
                    })
                    ->get();

                dump('greenlineReports fetched -- ' . date('Y-m-d H:i:s'));

                // Process each Greenline report using the GreenlineICIntegration method
                $this->processGreenlineReports($greenlineReports);

                // Update the Greenline reports to mark them as 'done'
                DB::table('greenline_reports')
                    ->where('report_id', $report->id)
                    ->update(['status' => 'done']);

                // Mark the report as completed
                DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);

            } catch (\Exception $e) {
                // Log any errors encountered during processing
                Log::error('Error in Greenline reconciliation: ' . $e->getMessage());

                // Mark the report as failed if there's an error
                DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);

                continue; // Move on to the next report
            }
        }
    }
}

// Run the reconciliation process
$reconciliation = new GreenLineReconciliation();
$reconciliation->runReconciliation();

print_r('Reconciliation process completed successfully.');
