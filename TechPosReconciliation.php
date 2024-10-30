<?php
//use App\Traits\TechPosIntegration; // Ensure this trait exists
//use Illuminate\Support\Facades\DB;
//use App\Models\TechPosReport; // Ensure you have this model set up
//use Illuminate\Support\Facades\Log;
//
//class TechPosReconciliation
//{
//    use TechPosIntegration; // Use the TechPosIntegration trait
//
//    /**
//     * Run the reconciliation process for TechPos reports.
//     */
//    public function runReconciliation()
//    {
//        // Set the limit for reports to process
//        $limit = 1; // You can adjust this limit
//
//        // Fetch pending TechPos reports from the 'reports' table
//        $reports = DB::table('reports')
//            ->where('pos', 'techpos')
//            ->where('status', 'pending')
//            ->limit($limit)
//            ->get();
//
//        foreach ($reports as $report) {
//            dump($report->id . '  -- ' . date('Y-m-d H:i:s'));
//
//            try {
//                // Mark report as started
//                DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);
//
//                // Retrieve TechPos reports related to this report
//                $techPosReports = TechPosReport::where('report_id', $report->id)
//                    ->where(function ($query) {
//                        $query->where('status', 'pending')
//                              ->orWhere('status', 'error');
//                    })
//                    ->get();
//
//                dump('TechPos reports fetched -- ' . date('Y-m-d H:i:s'));
//
//                // Process each TechPos report using the TechPosIntegration method
//                $this->processTechPosReports($techPosReports);
//
//                // Update the TechPos reports to mark them as 'done'
//                DB::table('tech_pos_reports')
//                    ->where('report_id', $report->id)
//                    ->update(['status' => 'done']);
//
//                // Mark the report as completed
//                DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);
//
//            } catch (\Exception $e) {
//                // Log any errors encountered during processing
//                Log::error('Error in TechPos reconciliation: ' . $e->getMessage());
//
//                // Mark the report as failed if there's an error
//                DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
//
//                continue; // Move on to the next report
//            }
//        }
//    }
//}
//
//// Run the reconciliation process
//$techPosReconciliation = new TechPosReconciliation();
//$techPosReconciliation->runReconciliation();
//
//print_r('Reconciliation process completed successfully.');
//

use App\Traits\ICIntegrationTrait;
use Illuminate\Support\Facades\DB;
use App\Models\TechPOSReport;
use Illuminate\Support\Facades\Log;


$report = DB::table('reports')->where('pos', 'techpos')->where('status', 'pending')->first();
dump($report->id . '  -- ' . date('Y-m-d H:i:s'));
try {
    DB::beginTransaction();
    DB::table('reports')->where('id', $report->id)->update(['status' => 'reconciliation_start']);
    $techPOSReports = TechPosReport::where('report_id', $report->id)->where('status', 'pending')->get();
    dump('techPOSReports fetched -- ' . date('Y-m-d H:i:s'));
    $cleanSheet = [];
    $insertionCount = 1;
    $insertionLimit = 500;
    $totalReportCount = count($techPOSReports);
    foreach ($techPOSReports as $key => $techPOSReport) {
        $cleanSheet[] = (new class {
            use ICIntegrationTrait;
        })->techPOSMasterCatalouge($techPOSReport, $report);
        $insertionCount++;
        if ($insertionCount == $insertionLimit || $key === $totalReportCount - 1) {
            DB::table('clean_sheets')->insert($cleanSheet);
            $insertionCount = 1;
            $cleanSheet = [];
        }
        if ($key === $totalReportCount - 1) {
            DB::table('tech_pos_reports')->where('report_id', $report->id)->update(['status' => 'done']);
        }
    }
    DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_process']);
    DB::commit();
} catch (\Exception $e) {
    Log::error('Error in TechPOS reconciliation: ' . $e->getMessage());
    DB::rollBack();
    DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);

}


print_r('Reconciliation process completed successfully.');

