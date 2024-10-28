<?php

namespace App\Traits;

use App\Models\ProfitTechReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProfitTechInventoryLog;
use App\Traits\ProfitTechICIntegration;

class ProfitTechReconciliation
{
    use ProfitTechIntegration;

    /**
     * Run the reconciliation process for ProfitTech reports.
     */
    public function runReconciliation()
    {
        // Set the limit for reports to process
        $limit = 1; // You can adjust this limit as needed

        // Fetch pending ProfitTech reports from the 'reports' table
        $reports = DB::table('reports')
            ->where('pos', 'profittech')
            ->where('status', 'pending')
            ->limit($limit)
            ->get();

        foreach ($reports as $report) {
            dump($report->id . '  -- ' . date('Y-m-d H:i:s'));

            try {
                // Mark report as started
                DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);

                // Retrieve ProfitTech reports related to this report
                $profitTechReports =  ProfitTechInventoryLog::where('report_id', $report->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                              ->orWhere('status', 'error');
                    })
                    ->get();

                dump('ProfitTechReports fetched -- ' . date('Y-m-d H:i:s'));

                // Process each ProfitTech report using the ProfitTechICIntegration method
                $this->processProfitTechReports($profitTechReports);

                // Update the ProfitTech reports to mark them as 'done'
                DB::table('profittech_pos_reports')
                    ->where('report_id', $report->id)
                    ->update(['status' => 'done']);

                // Mark the report as completed
                DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);

            } catch (\Exception $e) {
                // Log any errors encountered during processing
                Log::error('Error in ProfitTech reconciliation: ' . $e->getMessage());

                // Mark the report as failed if there's an error
                DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);

                continue; // Move on to the next report
            }
        }
    }
}

// Run the reconciliation process
$reconciliation = new ProfitTechReconciliation();
$reconciliation->runReconciliation();

print_r('ProfitTech Reconciliation process completed successfully.');
