<?php
namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Retailer;
use App\Models\Province;
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

    // Check if the user is a retailer
    if ($user->hasRole('Retailer')) {
        // Fetch reports only for the logged-in retailer
        $reports = Report::with('retailer')->where('retailer_id', $user->id)->get();
    } else {
        // Super admin: Fetch all reports
        $reports = Report::with('retailer')->get();
    }

    return view('reports.index', compact('reports'));
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

    // public function import(Request $request, $retailerId)
    // {
    //     $request->validate([
    //         'inventory_log_summary' => 'required|file|mimes:xlsx,xls,csv',
    //         'location' => 'required|string|max:255',
    //     ]);

    //     // Store the uploaded file
    //     $path = $request->file('inventory_log_summary')->store('uploads');

    //     // Import the GreenLine report and check for errors
    //     $import = new GreenLineReportImport($request->location);
    //     Excel::import($import, $path);

    //     // Get errors after import
    //     $importErrors = $import->getErrors();

    //     if (!empty($importErrors)) {
    //         return redirect()->back()->with('error', implode(', ', $importErrors));
    //     }

    //     return redirect()->back()->with('success', 'Greenline report imported successfully!');
    // }

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

    $province = Province::where('name', $address->province)->first();
    // dd($address);
    if (!$province) {
        return redirect()->back()->withErrors('Province not found.');
    }


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
        'location' => $request->location,
        'pos' => $request->pos,
        'province' => $province->name,
        'province_id' => $province->id,
        'province_slug' => $province->slug,
        'submitted_by' => auth()->id(), // Assuming you're using Laravel's auth
        'status' => 'pending', // Default status
        'date' => now()->startOfMonth(),  
    ]);
        // Initialize file paths
        $file1Path = null;
        $file2Path = null;
    

        // Check if POS system requires single file or multiple files
        if ($request->pos === 'cova') {
            if ($request->hasFile('diagnostic_report') && $request->hasFile('sales_summary_report')) {
                $file1Path = $request->file('diagnostic_report')->store('uploads');
                $file2Path = $request->file('sales_summary_report')->store('uploads');
        
                try {
                    // Import diagnostic report and check for errors
                    $diagnosticImport = new CovaDiagnosticReportImport($request->location, $report->id);
                    Excel::import($diagnosticImport, $file1Path);
                    
                    // Check for errors from diagnostic import
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
                $file1Path = $request->file('diagnostic_report')->store('uploads');
                $file2Path = $request->file('sales_summary_report')->store('uploads');
        
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
                $file1Path = $request->file('diagnostic_report')->store('uploads');
                $file2Path = $request->file('sales_summary_report')->store('uploads');
        
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
                $file1Path = $request->file('diagnostic_report')->store('uploads');
                $file2Path = $request->file('sales_summary_report')->store('uploads');
        
                // Import Ideal diagnostic report and check for errors
                $diagnosticImport = new IdealDiagnosticReportImport($request->location, $report->id);
                Excel::import($diagnosticImport, $file1Path);
                $diagnosticImportErrors = $diagnosticImport->getErrors();
        
                if (!empty($diagnosticImportErrors)) {
                    // Show the missing headers for diagnostic report
                    $errorMessage = 'Diagnostic report errors: ' . implode(', ', $diagnosticImportErrors);
                    return redirect()->back()->with('error', $errorMessage);
                }
        
                // Retrieve the ID of the last inserted diagnostic report
                // $diagnosticReportId = $diagnosticImport->getLastInsertedId();  // Assuming you added a method to retrieve the last inserted ID
        
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
                $file1Path = $request->file('inventory_log_summary')->store('uploads');
        
                // Import ProfitTech inventory log summary and handle exceptions
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
            $file1Path = $request->file('inventory_log_summary')->store('uploads');
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
                    $file1Path = $request->file('inventory_log_summary')->store('uploads');
            
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
            
                    // Continue with any further processing after a successful import
                    // ...
                }
            
            
            
            } elseif ($request->pos === 'barnet') {
                // Handle Barnet POS Report
                if ($request->hasFile('inventory_log_summary')) {
                    $file1Path = $request->file('inventory_log_summary')->store('uploads');
            
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
                // Handle OtherPOS uploads
                if ($request->hasFile('inventory_log_summary')) {
                    $file1Path = $request->file('inventory_log_summary')->store('uploads');
                    
                    // Import OtherPOS report
                    $import = new OtherPOSReportImport($request->location, $report->id);
                    Excel::import($import, $file1Path);
            
                    // Check for errors after import
                    if (!empty($import->getErrors())) {
                        return redirect()->back()->withErrors($import->getErrors());
                    }
                } else {
                    return redirect()->back()->withErrors('The report file is required for Other POS.');
                }
            }
            
            }
            
        

        // Update report record with file paths
        $report->update([
            'file_1' => $file1Path,
            'file_2' => $file2Path,
        ]);

        return redirect()->route('retailers.show', $retailerId)->with('success', 'Report added successfully.');
    }
}
