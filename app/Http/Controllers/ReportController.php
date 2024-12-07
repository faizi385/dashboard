<?php
namespace App\Http\Controllers;

use App\Models\BarnetPosReport;
use App\Models\CleanSheet;
use App\Models\CovaDiagnosticReport;
use App\Models\CovaSalesReport;
use App\Models\GlobalTillDiagnosticReport;
use App\Models\GlobalTillSalesSummaryReport;
use App\Models\GreenlineReport;
use App\Models\IdealDiagnosticReport;
use App\Models\IdealSalesSummaryReport;
use App\Models\OtherPOSReport;
use App\Models\ProfitTechInventoryLog;
use App\Models\Report;
use App\Models\Retailer;
use App\Models\Province;
use App\Models\TechPOSReport;
use App\Models\TendySalesSummaryReport;
use Illuminate\Support\Facades\Storage;
use App\Exports\CleanSheetsExport;
use App\Models\LP;
use Carbon\Carbon;
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
        $date = $request->get('month');
        if(!empty($date)){
            $date = Carbon::parse($date)->format('Y-m-01');
        }
        else{
            $date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');
        }
        $user = auth()->user();
        if ($user->hasRole('Retailer')) {
            $retailers = Retailer::where('user_id', $user->id)->first();
            if ($retailers) {
                $reports = Report::where('reports.retailer_id',$retailers->id)
                    ->where('date', $date)
                    ->with(['retailer'])
                    ->whereHas('retailer', function ($q) {
                        return $q->where('retailers.status', 'Approved');
                    })
                    ->leftJoin('retailer_statements', function ($join) {
                        $join->on('retailer_statements.report_id', '=', 'reports.id')
                            ->where('retailer_statements.flag', 0);
                    })
                    ->leftJoin('provinces', function ($join) {
                        $join->on('reports.province_id', '=', 'provinces.id');
                    })
                    ->select(
                        'reports.id',
                        'reports.retailer_id',
                        'reports.status',
                        'reports.province',
                        'reports.location',
                        'reports.date',
                        'reports.submitted_by',
                        'reports.pos',
                        'reports.file_1',
                        'reports.file_2',
                        'reports.created_at',
                        'reports.updated_at',
                        DB::raw('SUM(retailer_statements.total_fee) as total_fee_sum'),
                        DB::raw('ROUND(SUM(retailer_statements.total_fee + (retailer_statements.total_fee * IFNULL(provinces.tax_value, 5) / 100)), 2) as total_payout_with_tax')
                    )
                    ->groupBy('reports.id','reports.retailer_id','reports.status','reports.province',
                        'reports.location','reports.date','reports.submitted_by','reports.pos','reports.file_1',
                        'reports.file_2','reports.created_at','reports.updated_at'
                    )
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            } else {
                $reports = collect();
            }
        } elseif ($user->hasRole('LP')) {
            $lp = LP::where('user_id', $user->id)->first();

            if ($lp) {
                $reports = Report::where('reports.lp_id',$lp->id)
                    ->where('date', $date)
                    ->with(['retailer'])
                    ->whereHas('retailer', function ($q) {
                        return $q->where('retailers.status', 'Approved');
                    })
                    ->leftJoin('retailer_statements', function ($join) {
                        $join->on('retailer_statements.report_id', '=', 'reports.id')
                            ->where('retailer_statements.flag', 0);
                    })
                    ->leftJoin('provinces', function ($join) {
                        $join->on('reports.province_id', '=', 'provinces.id');
                    })
                    ->select(
                        'reports.id',
                        'reports.lp_id',
                        'reports.retailer_id',
                        'reports.status',
                        'reports.province',
                        'reports.location',
                        'reports.date',
                        'reports.submitted_by',
                        'reports.pos',
                        'reports.file_1',
                        'reports.file_2',
                        'reports.created_at',
                        'reports.updated_at',
                        DB::raw('SUM(retailer_statements.total_fee) as total_fee_sum'),
                        DB::raw('ROUND(SUM(retailer_statements.total_fee + (retailer_statements.total_fee * IFNULL(provinces.tax_value, 5) / 100)), 2) as total_payout_with_tax')
                    )
                    ->groupBy('reports.id','reports.lp_id','reports.retailer_id','reports.status','reports.province',
                        'reports.location','reports.date','reports.submitted_by','reports.pos','reports.file_1',
                        'reports.file_2','reports.created_at','reports.updated_at'
                    )
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            } else {
                $reports = collect();
            }
        } else {
            $reports = Report::
                where('date', $date)
                ->with(['retailer'])
                ->whereHas('retailer', function ($q) {
                    return $q->where('retailers.status', 'Approved');
                })
                ->leftJoin('retailer_statements', function ($join) {
                    $join->on('retailer_statements.report_id', '=', 'reports.id')
                        ->where('retailer_statements.flag', 0);
                })
                ->leftJoin('provinces', function ($join) {
                    $join->on('reports.province_id', '=', 'provinces.id');
                })
                ->select(
                    'reports.id',
                    'reports.retailer_id',
                    'reports.status',
                    'reports.province',
                    'reports.location',
                    'reports.date',
                    'reports.submitted_by',
                    'reports.pos',
                    'reports.file_1',
                    'reports.file_2',
                    'reports.created_at',
                    'reports.updated_at',
                    DB::raw('SUM(retailer_statements.total_fee) as total_fee_sum'),
                    DB::raw('ROUND(SUM(retailer_statements.total_fee + (retailer_statements.total_fee * IFNULL(provinces.tax_value, 5) / 100)), 2) as total_payout_with_tax')
                )
                ->groupBy('reports.id','reports.retailer_id','reports.status','reports.province',
                    'reports.location','reports.date','reports.submitted_by','reports.pos','reports.file_1',
                    'reports.file_2','reports.created_at','reports.updated_at'
                )
                ->orderBy('updated_at', 'DESC')
                ->get();
        }

        return view('reports.index', compact('reports', 'retailers','date'));

    }

    public function create($retailerId)
    {
        $retailer = Retailer::findOrFail($retailerId);

        $addresses = $retailer->address;

        return view('reports.create', compact('retailer', 'addresses'));
    }

    public function downloadFile($reportId, $fileNumber)
    {
        $report = Report::findOrFail($reportId);

        if ($fileNumber == 1) {
            $filePath = $report->file_1;
        } elseif ($fileNumber == 2) {
            $filePath = !empty($report->file_2) ? $report->file_2 : $report->file_1;
        } else {
            return redirect()->back()->with('error', 'Invalid file selection.');
        }

        if (empty($filePath) || !Storage::exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return Storage::download($filePath);
    }

    public function exportCleanSheets($report_id)
    {
        return Excel::download(new CleanSheetsExport($report_id), 'clean_sheets_report.xlsx');
    }


    public function exportStatement($report_id)
    {
        return Excel::download(new RetailerStatementExport($report_id), 'Distributor Statement.xlsx');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $report = Report::findOrFail($id);

            if ($report->pos == 'greenline') {
                GreenlineReport::where('report_id', $report->id)->delete();
            }
            if ($report->pos == 'techpos') {
                TechPOSReport::where('report_id', $report->id)->delete();
            }
            if ($report->pos == 'otherpos') {
                OtherPOSReport::where('report_id', $report->id)->delete();
            }
            if ($report->pos == 'barnet') {
                BarnetPosReport::where('report_id', $report->id)->delete();
            }
            if ($report->pos == 'profittech') {
                ProfitTechInventoryLog::where('report_id', $report->id)->delete();
            }
            if ($report->pos == 'global') {
                $globalDiagnostics = GlobalTillDiagnosticReport::where('report_id', $report->id)->get();
                foreach ($globalDiagnostics as $globalDiagnostic) {
                    GlobalTillSalesSummaryReport::where('gb_diagnostic_report_id', $globalDiagnostic->id)->delete();
                }
                GlobalTillDiagnosticReport::where('report_id', $report->id)->delete();
            }
            if ($report->pos == 'ideal') {
                $idealDiagnostics = IdealDiagnosticReport::where('report_id', $report->id)->get();
                foreach ($idealDiagnostics as $idealDiagnostic) {
                    IdealSalesSummaryReport::where('ideal_diagnostic_report_id', $idealDiagnostic->id)->delete();
                }
                IdealDiagnosticReport::where('report_id', $report->id)->delete();
            }
            if ($report->pos == 'tendy') {
                $tendyDiagnostics = TendyDiagnosticReport::where('report_id', $report->id)->get();
                foreach ($tendyDiagnostics as $tendyDiagnostic) {
                    TendySalesSummaryReport::where('diagnostic_report_id', $tendyDiagnostic->id)->delete();
                }
                TendyDiagnosticReport::where('report_id', $report->id)->delete();
            }
            if ($report->pos == 'cova') {
                $coavDiagnostics = CovaDiagnosticReport::where('report_id', $report->id)->get();
                foreach ($coavDiagnostics as $coavDiagnostic) {
                    CovaSalesReport::where('cova_diagnostic_report_id', $coavDiagnostic->id)->delete();
                }
                CovaDiagnosticReport::where('report_id', $report->id)->delete();
            }

            CleanSheet::where('report_id', $report->id)->delete();
            RetailerStatement::where('report_id', $report->id)->delete();

            $report->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Report deleted successfully');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
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
            $address = RetailerAddress::find($request->location);
            if (!$retailer || !$address) {
                return redirect()->back()->with('error','Retailer or Retailer Address not found.');
            }
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
            $existingReport = Report::where('retailer_id', $retailerId)
                ->where('pos', $request->pos)
                ->where('province', $province->name)
                ->whereYear('date', now()->startOfMonth()->subMonth()->year)
                ->whereMonth('date', now()->startOfMonth()->subMonth()->month)
                ->first();
            if ($existingReport) {
                return redirect()->back()->with('error', 'Report has already been uploaded for this POS and province this month.');
            }
            $lpId = $retailer->lp_id ?? null;
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
                'date' => now()->startOfMonth()->subMonth(),
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
                            return redirect()->back()->with('error',$diagnosticImport->getErrors());
                        }
                    } catch (\Exception $e) {
                        return redirect()->back()->with('error', 'Diagnostic report errors: ' . $e->getMessage());
                    }
                    try {
                        $salesImport = new CovaSalesReportImport($request->location, $report->id, $diagnosticImport->getId());
                        Excel::import($salesImport, $file2Path);
                        if ($salesImport->getErrors()) {
                            return redirect()->back()->with('error',$salesImport->getErrors());
                        }
                    } catch (\Exception $e) {
                        return redirect()->back()->with('error', 'Sales summary report errors: ' . $e->getMessage());
                    }
                } else {
                    return redirect()->back()->with('error','Both diagnostic and sales summary reports are required for COVA.');
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
                    return redirect()->back()->with('error','Both diagnostic and sales summary reports are required for TENDY.');
                }
            }elseif ($request->pos === 'ideal') {
                if ($request->hasFile('diagnostic_report') && $request->hasFile('sales_summary_report')) {
                    $file1Path = $request->file('diagnostic_report')->storeAs('uploads', $request->file('diagnostic_report')->getClientOriginalName());
                    $file2Path = $request->file('sales_summary_report')->storeAs('uploads', $request->file('sales_summary_report')->getClientOriginalName());
                    $diagnosticImport = new IdealDiagnosticReportImport($request->location, $report->id, $report->retailer_id, $report->lp_id);
                    Excel::import($diagnosticImport, $file1Path);
                    $diagnosticImportErrors = $diagnosticImport->getErrors();
                    if (!empty($diagnosticImportErrors)) {
                        $errorMessage = 'Diagnostic report errors: ' . implode(', ', $diagnosticImportErrors);
                        return redirect()->back()->with('error', $errorMessage);
                    }
                    $salesImport = new IdealSalesSummaryReportImport($request->location, $report->id, $diagnosticImport->getId());
                    Excel::import($salesImport, $file2Path);
                    $salesImportErrors = $salesImport->getErrors();
                    if (!empty($salesImportErrors)) {
                        $errorMessage = 'Sales summary report errors: ' . implode(', ', $salesImportErrors);
                        return redirect()->back()->with('error', $errorMessage);
                    }
                } else {
                    return redirect()->back()->with('error','Both diagnostic and sales summary reports are required for IDEAL.');
                }
            }elseif ($request->pos === 'profittech') {
                if ($request->hasFile('inventory_log_summary')) {
                    $file1Path = $request->file('inventory_log_summary')->storeAs('uploads', $request->file('inventory_log_summary')->getClientOriginalName());
                    try {
                        $import = new ProfitTechInventoryLogImport($request->location, $report->id, $report->retailer_id, $report->lp_id);
                        Excel::import($import, $file1Path);
                    } catch (\Exception $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    }
                    $importErrors = $import->getErrors();
                    if (!empty($importErrors)) {
                        $errorMessage = 'Missing header: ' . implode(', ', $importErrors);
                        return redirect()->back()->with('error', $errorMessage);
                    }
                } else {
                    return redirect()->back()->with('error','The inventory log summary file is required for ProfitTech.');
                }
            } elseif ($request->pos === 'global') {
                if ($request->hasFile('inventory_log_summary')) {
                    $file1Path = $request->file('inventory_log_summary')->storeAs('uploads', $request->file('inventory_log_summary')->getClientOriginalName());
                    try {
                        $diagnosticImport = new GlobalTillDiagnosticReportImport($request->location, $report->id ,$report->retailer_id, $report->lp_id);
                        Excel::import($diagnosticImport, $file1Path);
                    } catch (\Exception $e) {
                        return redirect()->back()->with('error', 'Diagnostic report errors: ' . $e->getMessage());
                    }
                } else {
                    return redirect()->back()->with('error','Diagnostic report is required for GLOBAL TILL.');
                }
            }
            elseif ($request->pos === 'greenline') {
                if ($request->hasFile('inventory_log_summary')) {
                    $file1Path = $request->file('inventory_log_summary')->storeAs('uploads', $request->file('inventory_log_summary')->getClientOriginalName());
                    $import = new GreenLineReportImport($request->location, $report->id, $report->retailer_id, $report->lp_id);
                    try {
                        Excel::import($import, $file1Path);
                        $importErrors = $import->getErrors();
                        if (!empty($importErrors)) {
                            return redirect()->back()->with('error', $importErrors[0]);
                        }
                    } catch (\Exception $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    }
                }
                else {
                    return redirect()->back()->with('error','The inventory log summary file is required for GLOBAL TILL.');
                }
            } elseif ($request->pos === 'techpos') {
                if ($request->hasFile('inventory_log_summary')) {
                    $file1Path = $request->file('inventory_log_summary')->storeAs('uploads', $request->file('inventory_log_summary')->getClientOriginalName());
                    $import = new TechPOSReportImport($request->location, $report->id, $report->retailer_id, $report->lp_id);
                    try {
                        Excel::import($import, $file1Path);
                        $importErrors = $import->getErrors();
                        if (!empty($importErrors)) {
                            return redirect()->back()->with('error', $importErrors[0]);
                        }
                    } catch (\Exception $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    }
                }
                else {
                    return redirect()->back()->with('error','The inventory log summary file is required for GLOBAL TILL.');
                }
            } elseif ($request->pos === 'barnet') {
                if ($request->hasFile('inventory_log_summary')) {
                    $file1Path = $request->file('inventory_log_summary')->storeAs('uploads', $request->file('inventory_log_summary')->getClientOriginalName());
                    $import = new BarnetPosReportImport($request->location, $report->id, $report->retailer_id, $report->lp_id);
                    try {
                        Excel::import($import, $file1Path);
                        $importErrors = $import->getErrors();
                        if (!empty($importErrors)) {
                            $errorMessage = implode(', ', $importErrors);
                            return redirect()->back()->with('error', $errorMessage);
                        }
                    } catch (\Exception $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    }
                } else {
                    return redirect()->back()->with('error','The inventory log summary file is required for Barnet.');
                }
            }
            elseif ($request->pos === 'otherpos') {
                if ($request->hasFile('inventory_log_summary')) {
                    $file1Path = $request->file('inventory_log_summary')->storeAs('uploads', $request->file('inventory_log_summary')->getClientOriginalName());
                    $import = new OtherPOSReportImport($request->location, $report->id, $report->retailer_id, $report->lp_id);
                    try {
                        Excel::import($import, $file1Path);
                        $importErrors = $import->getErrors();
                        if (!empty($importErrors)) {
                            $errorMessage = implode(', ', $importErrors);
                            return redirect()->back()->with('error', $errorMessage);
                        }
                    } catch (\Exception $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    }
                } else {
                    return redirect()->back()->with('error','The inventory log summary file is required for Other POS.');
                }
            }
            $report->update([
                'file_1' => $file1Path,
                'file_2' => $file2Path,
            ]);
            DB::commit();
            return redirect()->route('retailers.show', $retailerId)->with('success', 'Report added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
