<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Traits\RetailerStatementTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

$report = DB::table('reports')->where('status', 'Retailer Statement Process')->first();
if (!$report) {
    Log::info('No pending retailer statement found.');
    exit;
}
else {
    dump("Processing report ID: {$report->id} -- " . date('Y-m-d H:i:s'));
    try {
        DB::beginTransaction();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'Retailer Statement Start']);
        $retailerStatements = [];
        $insertionCount = 0;
        $insertionLimit = 500;
        $retailer_id = $report->retailer_id;
        $posRetailerStatement = $report->pos;
        $retailerStatementEntry = (new class {
            use RetailerStatementTrait;
        })->GenerateRetailerStatement($report, $retailer_id, $posRetailerStatement);
        if ($retailerStatementEntry) {
            $retailerStatementEntry['retailer_id'] = $retailer_id;
            $retailerStatements[] = $retailerStatementEntry;
            $insertionCount++;
        }
        if ($insertionCount > 0) {
            DB::table('retailer_statements')->insert($retailerStatements);
        }
        DB::table('reports')->where('id', $report->id)->update(['status' => 'Completed']);
        dump($report->id);
        DB::commit();
        print_r('Retailer statement process completed successfully.');

    } catch (\Exception $e) {
        Log::error('Error in retailer statement process: ' . $e->getMessage());
        DB::rollBack();
        DB::table('reports')->where('id', $report->id)->update(['status' => 'failed']);
    }
}
