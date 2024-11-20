<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Traits\RetailerStatementTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Fetch the report with status 'retailer_statement_process'
$report = DB::table('reports')->where('status', 'retailer_statement_process')->first();

if (!$report) {
    Log::info('No pending retailer statement found.');
    exit;
}

dump("Processing report ID: {$report->id} -- " . date('Y-m-d H:i:s'));

try {
    DB::beginTransaction();

    DB::table('reports')->where('id', $report->id)->update(['status' => 'retailer_statement_start']);
    
    // Initialize variables needed for batch processing
    $retailerStatements = [];
    $insertionCount = 0;
    $insertionLimit = 500;

    // Get retailer ID and POS data from the report
    $retailer_id = $report->retailer_id;
    $posRetailerStatement = $report->pos;

    // Assuming you want to process just one report, or else loop through multiple reports here
    $retailerStatementEntry = (new class {
        use RetailerStatementTrait;
    })->GenerateRetailerStatement($report, $retailer_id, $posRetailerStatement);

    if ($retailerStatementEntry) {
        // Ensure the retailer_id is included in the entry
        $retailerStatementEntry['retailer_id'] = $retailer_id; // Ensure retailer ID is set
        $retailerStatements[] = $retailerStatementEntry;
        $insertionCount++;
    }

    // Batch insert statements when reaching the limit
    if ($insertionCount > 0) {
        DB::table('retailer_statements')->insert($retailerStatements);
    }

    // Mark report as completed upon successful processing
    DB::table('reports')->where('id', $report->id)->update(['status' => 'Completed']);
    DB::commit();
    
    print_r('Retailer statement process completed successfully.');

} catch (\Exception $e) {
    // Log error and roll back transaction in case of failure
    Log::error('Error in retailer statement process: ' . $e->getMessage());
    DB::rollBack();
    
    // Update report status to "failed" if an exception occurs
    DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
}
