<?php
namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Retailer;
use App\Models\Province;
use Illuminate\Support\Facades\Storage;
use App\Exports\CleanSheetsExport;

use App\Exports\RetailerStatementExport;

use App\Models\RetailerStatement;
use App\Models\TendyDiagnosticReport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\{
    TechPosReportImport,
    BarnetPosReportImport,
    CovaSalesReportImport,
    GreenLineReportImport,
    CovaDiagnosticReportImport,
    IdealDiagnosticReportImport,
    TendyDiagnosticReportImport,
    ProfitTechInventoryLogImport,
    IdealSalesSummaryReportImport,
    TendySalesSummaryReportImport,
    GlobalTillDiagnosticReportImport,
    GlobalTillSalesSummaryReportImport,
    OtherPOSReportImport,
};
use App\Models\RetailerAddress;

class ReportController extends Controller
{

    public function index(Request $request, $retailer = null)
    {
        // Get the currently authenticated user
        $user = auth()->user();
        
        // Initialize an array to hold retailer sums
        $retailerSums = [];
        
        // Initialize total payout without tax for dashboard
        $totalPayoutAllRetailers = 0;
    
        // Ensure we are working with fresh data
        if ($user->hasRole('Retailer')) {
            // Fetch reports only for the logged-in retailer
            $reports = Report::with('retailer')->where('retailer_id', $user->id)->get();
            
            // Get retailer statements for the logged-in retailer
            $statements = RetailerStatement::where('retailer_id', $user->id)->get();
            
            // If statements are found, calculate the totals
            $totalPayout = $statements->sum('total_payout');
            $totalPayoutWithTax = $statements->sum('total_payout_with_tax');
            
            // Store the sums for the logged-in retailer
            $retailerSums[$user->id] = [
                'total_payout' => $totalPayout,
                'total_payout_with_tax' => $totalPayoutWithTax,
            ];
            
            // Add to total payout (without tax) for dashboard
            $totalPayoutAllRetailers = $totalPayout;
            
        } else {
            // Super admin: Fetch all reports
            $reports = Report::with('retailer')->get();
            
            foreach ($reports as $report) {
                $retailerId = $report->retailer_id; 
                
                // Ensure statements are fetched fresh for each retailer
                $statements = RetailerStatement::where('retailer_id', $retailerId)->get();
                
                // Initialize totals for the current retailer
                $totalPayout = 0;
                $totalPayoutWithTax = 0;
                
                foreach ($statements as $statement) {
                    // Ensure calculations are correct based on the data
                    $payout = $statement->quantity_sold * $statement->average_price;
                    $taxAmount = $payout * 0.13; // Assuming a fixed tax rate of 13%
                    $payoutWithTax = $payout + $taxAmount;
    
                    $totalPayout += $payout;
                    $totalPayoutWithTax += $payoutWithTax;
                }
                
                // Store the calculated sums for each retailer
                $retailerSums[$retailerId] = [
                    'total_payout' => $totalPayout,
                    'total_payout_with_tax' => $totalPayoutWithTax,
                ];
    
                // Add to total payout (without tax) for dashboard
                $totalPayoutAllRetailers += $totalPayout;
            }
        }
    
        // Pass the total payout without tax to the view (dashboard)
        return view('reports.index', compact('reports', 'retailerSums', 'totalPayoutAllRetailers'));
    }
    
    

    public function create($retailerId)
    {
        // Find the retailer by ID
        $retailer = Retailer::findOrFail($retailerId);

        // Get the addresses associated with the retailer
        $addresses = $retailer->address; // Assuming you have a relation set up

        // Return the view with retailer and addresses
        return view('reports.create', compact('retailer', 'addresses'));
    }

    public function downloadFile($reportId, $fileNumber)
    {
        $report = Report::findOrFail($reportId);
        
        // Determine the file path based on the requested file number
        $filePath = ($fileNumber == 1) ? $report->file_1 : $report->file_2;
    
        // Check if the file path exists
        if (!$filePath || !Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }
    
        // Download the file
        return Storage::download($filePath);
    }



  
    public function exportCleanSheets($report_id)
    {
   
        return Excel::download(new CleanSheetsExport($report_id), 'clean_sheets_report.xlsx');
    }
    

    public function exportStatement($report_id)
    {
        
        return Excel::download(new RetailerStatementExport($report_id), 'retailer_statement.xlsx');
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
    
       
    
        // Proceed with deletion
        $report->delete();
    
        return redirect()->back()->with('success', 'Report deleted successfully');
    }
    
    

  
    public function store(Request $request, $retailerId)
    {

    $request->validate([
        'location' => 'required|string|max:255',
        'pos' => 'required|string',
    ]);


    $retailer = Retailer::find($retailerId);
    $address = RetailerAddress::find($request->location);
    if (!$retailer || !$address) {
        return redirect()->back()->withErrors('Retailer or Retailer Address not found.');
    }

    $province = Province::where('id', $address->province)->first();
    // dd($address);
    if (!$province) {
        return redirect()->back()->with('error','Province not found.');
    }

    $locationString = "{$address->street_no} {$address->street_name}, {$address->city}, {$province->name}";
    // Check if a report already exists for the given location and POS in the current month
    $existingReport = Report::where('retailer_id', $retailerId)
        ->where('location', $request->location)
        ->whereYear('date', now()->year)
        ->whereMonth('date', now()->month)
        ->first();

    if ($existingReport) {
        return redirect()->back()->with('error', 'A report has already been uploaded for this location and POS this month.');
    }

    // Create the report record with province details, submitted_by, and status
    $report = Report::create([
        'retailer_id' => $retailerId,
        'location' => $locationString,
        'pos' => $request->pos,
        'province' => $province->name,
        'province_id' => $province->id,
        'province_slug' => $province->slug,
        'submitted_by' => auth()->id(), // Assuming you're using Laravel's auth
        'status' => 'pending', // Default status
        'date' => now()->startOfMonth(),
    ]);
       
        $file1Path = null;
        $file2Path = null;

        if ($request->pos === 'cova') {
            if ($request->hasFile('diagnostic_report') && $request->hasFile('sales_summary_report')) {
                $file1Path = $request->file('diagnostic_report')->storeAs('uploads', $request->file('diagnostic_report')->getClientOriginalName());
                $file2Path = $request->file('sales_summary_report')->storeAs('uploads', $request->file('sales_summary_report')->getClientOriginalName());

                try {
                    // Import diagnostic report and check for errors
                    $diagnosticImport = new CovaDiagnosticReportImport($request->location, $report->id);
                    Excel::import($diagnosticImport, $file1Path);

                    if ($diagnosticImport->getErrors()) {
                        return redirect()->back()->withErrors($diagnosticImport->getErrors());
                    }

                } catch (\Exception $e) {
                    // Catch any exceptions (including missing headers) and display the error
                    return redirect()->back()->with('error', 'Diagnostic report errors: ' . $e->getMessage());
                }

                try {
                    // Import sales summary report and check for errors
                    $salesImport = new CovaSalesReportImport($request->location, $report->id, $diagnosticImport->getId());
                    Excel::import($salesImport, $file2Path);

                    // Check for errors from sales summary import
                    if ($salesImport->getErrors()) {
                        return redirect()->back()->withErrors($salesImport->getErrors());
                    }

                } catch (\Exception $e) {
                    // Catch any exceptions (including missing headers) and display the error
                    return redirect()->back()->with('error', 'Sales summary report errors: ' . $e->getMessage());
                }

            } else {
                return redirect()->back()->withErrors('Both diagnostic and sales summary reports are required for COVA.');
            }

        }elseif ($request->pos === 'tendy') {
            if ($request->hasFile('diagnostic_report') && $request->hasFile('sales_summary_report')) {
                $file1Path = $request->file('diagnostic_report')->storeAs('uploads', $request->file('diagnostic_report')->getClientOriginalName());
                $file2Path = $request->file('sales_summary_report')->storeAs('uploads', $request->file('sales_summary_report')->getClientOriginalName());

                try {
                    // Import Tendy diagnostic report and check for errors
                    $diagnosticImport = new TendyDiagnosticReportImport($report->id, $request->location);
                    Excel::import($diagnosticImport, $file1Path);

                } catch (\Exception $e) {
                    // Catch any exceptions (including missing headers) and display the error
                    return redirect()->back()->with('error', 'Diagnostic report errors: ' . $e->getMessage());
                }

                try {
                    // Import Tendy sales summary report and check for errors
                    $salesSummaryImport = new TendySalesSummaryReportImport($request->location, $report->id);
                    Excel::import($salesSummaryImport, $file2Path);

                } catch (\Exception $e) {
                    // Catch any exceptions (including missing headers) and display the error
                    return redirect()->back()->with('error', 'Sales summary report errors: ' . $e->getMessage());
                }

            } else {
                return redirect()->back()->withErrors('Both diagnostic and sales summary reports are required for TENDY.');
            }

        }elseif ($request->pos === 'global') {
            // Check for both diagnostic and sales summary report files
            if ($request->hasFile('diagnostic_report') && $request->hasFile('sales_summary_report')) {
                $file1Path = $request->file('diagnostic_report')->storeAs('uploads', $request->file('diagnostic_report')->getClientOriginalName());
                $file2Path = $request->file('sales_summary_report')->storeAs('uploads', $request->file('sales_summary_report')->getClientOriginalName());

                try {
                    // Import Global Till diagnostic report and check for errors
                    $diagnosticImport = new GlobalTillDiagnosticReportImport($request->location, $report->id);
                    Excel::import($diagnosticImport, $file1Path);

                    // Get the ID of the imported diagnostic report
                    $diagnosticReportId = $diagnosticImport->getId();

                } catch (\Exception $e) {
                    // Catch any exceptions (including missing headers) and display the error
                    return redirect()->back()->with('error', 'Diagnostic report errors: ' . $e->getMessage());
                }

                try {
                    // Import Global Till sales summary report and include diagnostic report ID
                    $salesImport = new GlobalTillSalesSummaryReportImport($request->location, $report->id, $diagnosticReportId);
                    Excel::import($salesImport, $file2Path);

                } catch (\Exception $e) {
                    // Catch any exceptions (including missing headers) and display the error
                    return redirect()->back()->with('error', 'Sales summary report errors: ' . $e->getMessage());
                }

            } else {
                return redirect()->back()->withErrors('Both diagnostic and sales summary reports are required for GLOBAL TILL.');
            }

        } elseif ($request->pos === 'ideal') {
            if ($request->hasFile('diagnostic_report') && $request->hasFile('sales_summary_report')) {
                $file1Path = $request->file('diagnostic_report')->storeAs('uploads', $request->file('diagnostic_report')->getClientOriginalName());
                $file2Path = $request->file('sales_summary_report')->storeAs('uploads', $request->file('sales_summary_report')->getClientOriginalName());

                // Import Ideal diagnostic report and check for errors
                $diagnosticImport = new IdealDiagnosticReportImport($request->location, $report->id);
                Excel::import($diagnosticImport, $file1Path);
                $diagnosticImportErrors = $diagnosticImport->getErrors();

                if (!empty($diagnosticImportErrors)) {
                    // Show the missing headers for diagnostic report
                    $errorMessage = 'Diagnostic report errors: ' . implode(', ', $diagnosticImportErrors);
                    return redirect()->back()->with('error', $errorMessage);
                }

                // Import Ideal sales summary report and check for errors
                $salesImport = new IdealSalesSummaryReportImport($request->location, $report->id, $diagnosticImport->getId());
                Excel::import($salesImport, $file2Path);
                $salesImportErrors = $salesImport->getErrors();

                if (!empty($salesImportErrors)) {
                    // Show the missing headers for sales summary report
                    $errorMessage = 'Sales summary report errors: ' . implode(', ', $salesImportErrors);
                    return redirect()->back()->with('error', $errorMessage);
                }

            } else {
                return redirect()->back()->withErrors('Both diagnostic and sales summary reports are required for IDEAL.');
            }

        }elseif ($request->pos === 'profittech') {
            // Handle ProfitTech Inventory Log Summary
            if ($request->hasFile('inventory_log_summary')) {
                $file1Path = $request->file('inventory_log_summary')->storeAs('uploads', $request->file('inventory_log_summary')->getClientOriginalName());

                try {
                    $import = new ProfitTechInventoryLogImport($request->location, $report->id);
                    Excel::import($import, $file1Path);
                } catch (\Exception $e) {
                    // Catch the exception and display the error message
                    return redirect()->back()->with('error', $e->getMessage());
                }

                // Check for other errors (if any)
                $importErrors = $import->getErrors();

                // If there are any errors, show them in the toaster
                if (!empty($importErrors)) {
                    // Show the missing headers in a single message
                    $errorMessage = 'Missing header: ' . implode(', ', $importErrors);

                    return redirect()->back()->with('error', $errorMessage);
                }
            } else {
                return redirect()->back()->withErrors('The inventory log summary file is required for ProfitTech.');
            }

        } elseif ($request->hasFile('inventory_log_summary')) {
            $file1Path = $request->file('inventory_log_summary')->storeAs('uploads', $request->file('inventory_log_summary')->getClientOriginalName());
            if ($request->pos === 'greenline') {
                // Import GreenLine report and check for errors
                $import = new GreenLineReportImport($request->location, $report->id);

                try {
                    Excel::import($import, $file1Path);

                    // Get any import errors
                    $importErrors = $import->getErrors();

                    // If there are errors, redirect back with a single error message
                    if (!empty($importErrors)) {
                        return redirect()->back()->with('error', $importErrors[0]);
                    }
                } catch (\Exception $e) {
                    // Redirect back with the error message from the exception
                    return redirect()->back()->with('error', $e->getMessage());
                }


            } elseif ($request->pos === 'techpos') {
                // Handle TechPOS Report
                if ($request->hasFile('inventory_log_summary')) {
                    $file1Path = $request->file('inventory_log_summary')->storeAs('uploads', $request->file('inventory_log_summary')->getClientOriginalName());

                    // Import TechPOS report and check for errors
                    $import = new TechPOSReportImport($request->location, $report->id);

                    try {
                        Excel::import($import, $file1Path);

                        // Get any import errors
                        $importErrors = $import->getErrors();

                        // If there are errors, redirect back with a single error message
                        if (!empty($importErrors)) {
                            return redirect()->back()->with('error', $importErrors[0]);
                        }
                    } catch (\Exception $e) {
                        // Redirect back with the error message from the exception
                        return redirect()->back()->with('error', $e->getMessage());
                    }

                }

            } elseif ($request->pos === 'barnet') {
                // Handle Barnet POS Report
                if ($request->hasFile('inventory_log_summary')) {
                    $file1Path = $request->file('inventory_log_summary')->storeAs('uploads', $request->file('inventory_log_summary')->getClientOriginalName());

                    // Import Barnet POS report and check for errors
                    $import = new BarnetPosReportImport($request->location, $report->id);

                    try {
                        Excel::import($import, $file1Path);
                        $importErrors = $import->getErrors();

                        if (!empty($importErrors)) {
                            // Show the missing headers in a single message
                            $errorMessage = implode(', ', $importErrors);
                            return redirect()->back()->with('error', $errorMessage);
                        }
                    } catch (\Exception $e) {
                        // Handle exceptions thrown by the import process
                        return redirect()->back()->with('error', $e->getMessage());
                    }
                } else {
                    return redirect()->back()->withErrors('The inventory log summary file is required for Barnet.');
                }
            }

            elseif ($request->pos === 'otherpos') {
                // Handle OtherPOS Report
                if ($request->hasFile('inventory_log_summary')) {
                    $file1Path = $request->file('inventory_log_summary')->storeAs('uploads', $request->file('inventory_log_summary')->getClientOriginalName());
            
                    // Import OtherPOS report and check for errors
                    $import = new OtherPOSReportImport($request->location, $report->id);
            
                    try {
                        Excel::import($import, $file1Path);
                        $importErrors = $import->getErrors();
            
                        if (!empty($importErrors)) {
                            // Show the missing headers in a single message
                            $errorMessage = implode(', ', $importErrors);
                            return redirect()->back()->with('error', $errorMessage);
                        }
                    } catch (\Exception $e) {
                        // Handle exceptions thrown by the import process
                        return redirect()->back()->with('error', $e->getMessage());
                    }
                } else {
                    return redirect()->back()->withErrors('The inventory log summary file is required for Other POS.');
                }
            }
            

            }

        $report->update([
            'file_1' => $file1Path,
            'file_2' => $file2Path,
        ]);

        return redirect()->route('retailers.show', $retailerId)->with('success', 'Report added successfully.');
    }
}
