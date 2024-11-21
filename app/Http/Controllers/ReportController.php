<?php
namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Retailer;
use App\Models\Province;
use Illuminate\Support\Facades\Storage;
use App\Exports\CleanSheetsExport;
use App\Models\LP;
use App\Exports\RetailerStatementExport;
use Illuminate\Support\Facades\DB;
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
    public function index(Request $request, $retailers = '')
    {
        // Get the currently authenticated user
        $user = auth()->user();

        if ($user->hasRole('Retailer')) {
            // Attempt to find the retailer associated with the user
            $retailers = Retailer::where('user_id', $user->id)->first();

            if ($retailers) {
                // Fetch reports and statements for the retailer
                $reports = Report::with('retailer')->where('retailer_id', $retailers->id)->get();
                $statements = RetailerStatement::where('retailer_id', $retailers->id)->get();
            } else {
                // Empty collections if no retailer found
                $reports = collect();
                $statements = collect();
            }
        } elseif ($user->hasRole('LP')) {
            // Get LP details based on the user ID
            $lp = LP::where('user_id', $user->id)->first();

            if ($lp) {
                // Fetch reports uploaded by the LP
                $reports = Report::with('retailer')->where('lp_id', $lp->id)->get();
                $statements = RetailerStatement::whereHas('report', function ($query) use ($lp) {
                    $query->where('lp_id', $lp->id);
                })->get();
            } else {
                // Empty collections if no LP found
                $reports = collect();
                $statements = collect();
            }
        } else {
            // For Super Admin: Fetch all reports and statements
            $reports = Report::with('retailer')->get();
            $statements = RetailerStatement::all();
        }

        // Initialize arrays for sums
        $retailerSumsByLocation = [];
        $totalPayoutWithoutTax = 0;
        $totalPayoutWithTax = 0;

        // Loop through each statement to calculate total fees and taxes by location
        foreach ($statements as $statement) {
            // Get the location of the statement (from the Report model)
            $location = $statement->report->location ?? null;

            // Calculate the tax rate for the location (based on province)
            $province = $statement->report->province ?? null;
            $taxRate = $this->getProvinceTaxRate($province);

            // Calculate the payout with tax
            $payoutWithTax = $statement->total_fee * (1 + $taxRate);

            // Add to total payout without tax and with tax
            $totalPayoutWithoutTax += $statement->total_fee;
            $totalPayoutWithTax += $payoutWithTax;

            // Sum total fees and payout with tax by location
            if (!isset($retailerSumsByLocation[$location])) {
                $retailerSumsByLocation[$location] = [
                    'total_fee_sum' => 0,
                    'total_payout_with_tax' => 0,
                ];
            }

            // Update sums for the specific location
            $retailerSumsByLocation[$location]['total_fee_sum'] += $statement->total_fee;
            $retailerSumsByLocation[$location]['total_payout_with_tax'] += $payoutWithTax;
        }

        // Pass data to the view
        return view('reports.index', compact('reports', 'retailers', 'retailerSumsByLocation', 'totalPayoutWithoutTax', 'totalPayoutWithTax'));
    }




    // Helper function to get the tax rate based on the province
    private function getProvinceTaxRate($province)
    {
        $taxRates = [
            'Alberta' => 0.05,
            'Ontario' => 0.03,
            'Manitoba' => 0.05,
            'British Columbia' => 0.05,
            'Saskatchewan' => 0.05,
        ];

        return $taxRates[$province] ?? 0;
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
        if ($fileNumber == 1) {
            $filePath = $report->file_1;
        } elseif ($fileNumber == 2) {
            // If file_2 is not available, use file_1 for both file numbers
            $filePath = !empty($report->file_2) ? $report->file_2 : $report->file_1;
        } else {
            return redirect()->back()->with('error', 'Invalid file selection.');
        }

        // Check if the file path is empty or if the file does not exist in storage
        if (empty($filePath) || !Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Proceed to download the file if it exists
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
        DB::beginTransaction();
        try {
        $request->validate([
            'location' => 'required|string|max:255',
            'pos' => 'required|string',
        ]);

        $retailer = Retailer::find($retailerId);
        $address = RetailerAddress::find($request->location); // Assumes location is an address ID

        if (!$retailer || !$address) {
            return redirect()->back()->withErrors('Retailer or Retailer Address not found.');
        }

        // Concatenate address fields to create the location string
        $locationString = $address->street_no . ', ' .
                          $address->street_name . ', ' .
                          $address->city . ', ' .
                          $address->province;

        $existingLocation = Report::where('retailer_id', $retailerId)
            ->where('location', $locationString)
            ->first();

        if ($existingLocation) {
            return redirect()->back()->with('error', 'This location has already been used for a report.');
        }

        $province = Province::where('id', $address->province)->first();

        if (!$province) {
            return redirect()->back()->with('error', 'Province not found.');
        }

        // Check if a report for this POS, province, and month already exists
        $existingReport = Report::where('retailer_id', $retailerId)
            ->where('pos', $request->pos)
            ->where('province', $province->name)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->first();

        if ($existingReport) {
            return redirect()->back()->with('error', 'A report has already been uploaded for this POS and province this month.');
        }

    
        $lpId = $retailer->lp_id ?? null; 

        // dd( $request);
        $report = Report::create([
            'retailer_id' => $retailerId,
            'location' => $locationString,
            'address_id' => $request->location,
            'pos' => $request->pos,
            'province' => $province->name,
            'province_id' => $province->id,
            'province_slug' => $province->slug,
            'submitted_by' => auth()->id(),
            'status' => 'Pending',
            'date' => now()->startOfMonth(),
            'lp_id' => $lpId, 
        ]);

        $file1Path = null;
        $file2Path = null;

        if ($request->pos === 'cova') {
            if ($request->hasFile('diagnostic_report') && $request->hasFile('sales_summary_report')) {
                $file1Path = $request->file('diagnostic_report')->storeAs('uploads', $request->file('diagnostic_report')->getClientOriginalName());
                $file2Path = $request->file('sales_summary_report')->storeAs('uploads', $request->file('sales_summary_report')->getClientOriginalName());

                try {
          
                    $diagnosticImport = new CovaDiagnosticReportImport($request->location, $report->id, $report->retailer_id, $report->lp_id);
                    Excel::import($diagnosticImport, $file1Path);

                    if ($diagnosticImport->getErrors()) {
                        return redirect()->back()->withErrors($diagnosticImport->getErrors());
                    }

                } catch (\Exception $e) {
    
                    return redirect()->back()->with('error', 'Diagnostic report errors: ' . $e->getMessage());
                }

                try {
           
                    $salesImport = new CovaSalesReportImport($request->location, $report->id, $diagnosticImport->getId());
                    Excel::import($salesImport, $file2Path);

                    if ($salesImport->getErrors()) {
                        return redirect()->back()->withErrors($salesImport->getErrors());
                    }

                } catch (\Exception $e) {
     
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
          
                    $diagnosticImport = new TendyDiagnosticReportImport($report->id, $request->location, $report->retailer_id, $report->lp_id);
                    Excel::import($diagnosticImport, $file1Path);

                } catch (\Exception $e) {
    
                    return redirect()->back()->with('error', 'Diagnostic report errors: ' . $e->getMessage());
                }

                try {
       
                    $salesSummaryImport = new TendySalesSummaryReportImport($request->location, $report->id);
                    Excel::import($salesSummaryImport, $file2Path);

                } catch (\Exception $e) {

                    return redirect()->back()->with('error', 'Sales summary report errors: ' . $e->getMessage());
                }

            } else {
                return redirect()->back()->withErrors('Both diagnostic and sales summary reports are required for TENDY.');
            }

        }elseif ($request->pos === 'global') {
   
            if ($request->hasFile('diagnostic_report') && $request->hasFile('sales_summary_report')) {
                $file1Path = $request->file('diagnostic_report')->storeAs('uploads', $request->file('diagnostic_report')->getClientOriginalName());
                $file2Path = $request->file('sales_summary_report')->storeAs('uploads', $request->file('sales_summary_report')->getClientOriginalName());

                try {
  
                    $diagnosticImport = new GlobalTillDiagnosticReportImport($request->location, $report->id ,$report->retailer_id, $report->lp_id);
                    Excel::import($diagnosticImport, $file1Path);

                    $diagnosticReportId = $diagnosticImport->getId();

                } catch (\Exception $e) {
          
                    return redirect()->back()->with('error', 'Diagnostic report errors: ' . $e->getMessage());
                }

                try {
      
                    $salesImport = new GlobalTillSalesSummaryReportImport($request->location, $report->id, $diagnosticReportId);
                    Excel::import($salesImport, $file2Path);

                } catch (\Exception $e) {
           
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
                $diagnosticImport = new IdealDiagnosticReportImport($request->location, $report->id, $report->retailer_id, $report->lp_id);
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
                    $import = new ProfitTechInventoryLogImport($request->location, $report->id, $report->retailer_id, $report->lp_id);
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
                $import = new GreenLineReportImport($request->location, $report->id, $report->retailer_id, $report->lp_id);

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
                    $import = new TechPOSReportImport($request->location, $report->id, $report->retailer_id, $report->lp_id);

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
                    $import = new BarnetPosReportImport($request->location, $report->id, $report->retailer_id, $report->lp_id);

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
                    $import = new OtherPOSReportImport($request->location, $report->id, $report->retailer_id, $report->lp_id);

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

            DB::commit(); // Commit the transaction

            return redirect()->route('retailers.show', $retailerId)->with('success', 'Report added successfully.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of an error
            return redirect()->back()->with('error', $e->getMessage());
            // dd($e);
        }
    }
}
