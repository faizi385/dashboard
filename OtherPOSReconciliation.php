<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\Traits\OtherPOSIntegration;
use Illuminate\Support\Facades\Log;
use App\Models\OtherPOSReport; // Import your OtherPOSReport model

class OtherPOSReconciliation
{
    use OtherPOSIntegration; // Trait for Other POS specific integration

    /**
     * Run the reconciliation process for Other POS reports.
     */
    public function runReconciliation()
    {
        // Set the limit for reports to process
        $limit = 1; // You can adjust this limit

        // Fetch pending Other POS reports from the 'reports' table
        $reports = DB::table('reports')
            ->where('pos', 'otherpos')
            ->where('status', 'pending')
            ->limit($limit)
            ->get();

        foreach ($reports as $report) {
            dump($report->id . '  -- ' . date('Y-m-d H:i:s'));

            try {
                // Mark report as started
                DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);

                // Retrieve Other POS reports related to this report
                $otherPOSReports = OtherPOSReport::where('report_id', $report->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                              ->orWhere('status', 'error');
                    })
                    ->get();

                dump('Other POS reports fetched -- ' . date('Y-m-d H:i:s'));

                // Process each Other POS report
                $this->processOtherPOSReports($otherPOSReports);

                // Update the Other POS reports to mark them as 'done'
                DB::table('other_pos_reports')
                    ->where('report_id', $report->id)
                    ->update(['status' => 'done']);

                // Mark the report as completed
                DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);

            } catch (\Exception $e) {
                // Log any errors encountered during processing
                Log::error('Error in Other POS reconciliation: ' . $e->getMessage());

                // Mark the report as failed if there's an error
                DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
            }
        }
    }

    /**
     * Process Other POS reports.
     *
     * @param \Illuminate\Support\Collection $otherPOSReports
     * @return void
     */
 
}

// Run the reconciliation process
$reconciliation = new OtherPOSReconciliation();
$reconciliation->runReconciliation();

print_r('Reconciliation process for Other POS completed successfully.');
