<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Retailer;
use Illuminate\Http\Request;

use App\Imports\TechPosReportImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BarnetPosReportImport;
use App\Imports\CovaSalesReportImport;
use App\Imports\GreenlineReportImport;
use App\Imports\CovaDiagnosticReportImport;
use App\Imports\IdealDiagnosticReportImport;
use App\Imports\TendyDiagnosticReportImport;
use App\Imports\ProfitTechInventoryLogImport;
use App\Imports\IdealSalesSummaryReportImport;
use App\Imports\TendySalesSummaryReportImport;
use App\Imports\GlobalTillDiagnosticReportImport;
use App\Imports\GlobalTillSalesSummaryReportImport;


class ReportController extends Controller
{
    public function create($retailerId)
    {
        // Find the retailer by ID
        $retailer = Retailer::findOrFail($retailerId);
        
        // Get the addresses associated with the retailer
        $addresses = $retailer->address; // Assuming you have a relation set up

        // Return the view with retailer and addresses
        return view('reports.create', compact('retailer', 'addresses'));
    }

public function import(Request $request, $retailerId)
{
    $request->validate([
        'inventory_log_summary' => 'required|file|mimes:xlsx,xls,csv',
        'location' => 'required|string|max:255',
    ]);

    // Store the uploaded file
    $path = $request->file('inventory_log_summary')->store('uploads');

    // Use the import class to handle the data import
    Excel::import(new GreenLineReportImport($request->location), $path);

    return redirect()->back()->with('success', 'Greenline report imported successfully!');
}


public function store(Request $request, $retailerId)
{
    $request->validate([
        'location' => 'required|string|max:255',
        'pos' => 'required|string',
    ]);

    // Create the report record with submitted_by and status
    $report = Report::create([
        'retailer_id' => $retailerId,
        'location' => $request->location,
        'pos' => $request->pos,
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

            // Import diagnostic report
            Excel::import(new CovaDiagnosticReportImport($request->location, $report->id), $file1Path);
            // Import sales summary report
            Excel::import(new CovaSalesReportImport($request->location, $report->id), $file2Path);
        } else {
            return redirect()->back()->withErrors('Both diagnostic and sales summary reports are required for COVA.');
        }
    } elseif ($request->pos === 'tendy') {
        if ($request->hasFile('diagnostic_report') && $request->hasFile('sales_summary_report')) {
            $file1Path = $request->file('diagnostic_report')->store('uploads');
            $file2Path = $request->file('sales_summary_report')->store('uploads');

            // Import Tendy diagnostic and sales reports
            Excel::import(new TendyDiagnosticReportImport($request->location, $report->id), $file1Path);
            Excel::import(new TendySalesSummaryReportImport($request->location, $report->id), $file2Path);
        } else {
            return redirect()->back()->withErrors('Both diagnostic and sales summary reports are required for TENDY.');
        }
    } elseif ($request->pos === 'global') {
        // Handle Global Till uploads
        if ($request->hasFile('diagnostic_report') && $request->hasFile('sales_summary_report')) {
            $file1Path = $request->file('diagnostic_report')->store('uploads');
            $file2Path = $request->file('sales_summary_report')->store('uploads');

            // Import Global Till diagnostic and sales reports
            Excel::import(new GlobalTillDiagnosticReportImport($request->location, $report->id), $file1Path);
            Excel::import(new GlobalTillSalesSummaryReportImport($request->location, $report->id), $file2Path);
        } else {
            return redirect()->back()->withErrors('Both diagnostic and sales summary reports are required for GLOBAL TILL.');
        }
    } elseif ($request->pos === 'ideal') {
        if ($request->hasFile('diagnostic_report') && $request->hasFile('sales_summary_report')) {
            $file1Path = $request->file('diagnostic_report')->store('uploads');
            $file2Path = $request->file('sales_summary_report')->store('uploads');

            // Import Ideal diagnostic and sales reports
            Excel::import(new IdealDiagnosticReportImport($request->location, $report->id), $file1Path);
            // You need to create an import class for the Ideal Sales Summary Report
            Excel::import(new IdealSalesSummaryReportImport($request->location, $report->id), $file2Path);
        } else {
            return redirect()->back()->withErrors('Both diagnostic and sales summary reports are required for IDEAL.');
        }
    } elseif ($request->pos === 'profittech') {
        // Handle ProfitTech Inventory Log Summary
        if ($request->hasFile('inventory_log_summary')) {
            $file1Path = $request->file('inventory_log_summary')->store('uploads');

            // Import ProfitTech inventory log summary
            Excel::import(new ProfitTechInventoryLogImport($request->location, $report->id), $file1Path);
        } else {
            return redirect()->back()->withErrors('The inventory log summary file is required for ProfitTech.');
        }
    } elseif ($request->hasFile('inventory_log_summary')) {
        $file1Path = $request->file('inventory_log_summary')->store('uploads');

        if ($request->pos === 'greenline') {
            Excel::import(new GreenlineReportImport($request->location, $report->id), $file1Path);
        } elseif ($request->pos === 'techpos') {
            Excel::import(new TechPosReportImport($request->location, $report->id), $file1Path);
        } elseif ($request->pos === 'barnet') {
            Excel::import(new BarnetPosReportImport($request->location, $report->id), $file1Path);
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
