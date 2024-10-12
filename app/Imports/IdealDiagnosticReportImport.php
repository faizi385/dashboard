<?php

namespace App\Imports;

use App\Models\IdealDiagnosticReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IdealDiagnosticReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;

    public function __construct($location, $reportId)
    {
        $this->location = $location;
        $this->reportId = $reportId;
    }

    public function model(array $row)
    {
        return new IdealDiagnosticReport([
            'report_id' => $this->reportId,
            'sku' => $row['sku'],
            'description' => $row['description'],
            'opening' => $row['opening'],
            'purchases' => $row['purchases'],
            'returns' => $row['returns'],
            'trans_in' => $row['trans_in'],
            'trans_out' => $row['trans_out'],
            'unit_sold' => $row['unit_sold'],
            'write_offs' => $row['write_offs'],
            'closing' => $row['closing'],
            'net_sales_ex' => $row['net_sales_ex'],
        ]);
    }
}
