<?php

namespace App\Exports;

use App\Models\RetailerStatement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RetailerStatementExport implements FromCollection, WithMapping, WithHeadings, WithStyles
{
    private $report_id;

    public function __construct($report_id)
    {
        $this->report_id = $report_id;
    }

    public function collection()
    {
        return RetailerStatement::with(['report.retailer', 'product_variations'])
            ->where('report_id', $this->report_id)
            ->get();
    }

    public function map($row): array
    {
        $province = $this->getAllProvince($row->report->province ?? 'Unknown Province');
        $taxRate = $this->getProvinceTaxRate($province);
        $taxAmount = $row->total_fee * $taxRate;
        $totalFeeWithTax = $row->total_fee + $taxAmount;

        return [
            $row->lp_id ?? 'Unknown LP',
            $province,
            $this->clearValue($row->report->retailer->dba ?? 'Unknown DBA'),
            trim($this->clearValue($row->report->retailer->first_name ?? '') . ' ' . $this->clearValue($row->report->retailer->last_name ?? '')) ?: 'Unknown Retailer',
            $this->clearValue($row->product_name ?? 'Unknown Product'),  
            $this->clearValue($row->sku) ?? 'N/A',
            $this->clearValue($row->category) ?? 'N/A',
            $this->clearValue($row->brand) ?? 'N/A',
            $row->quantity,
            $row->quantity_sold,
            number_format((float)str_replace('$','',$row->average_price), 2, '.', ','),
            $row->opening_inventory_unit,
            $row->closing_inventory_unit,
            number_format((float)$row->unit_cost, 2, '.', ','),
            number_format((float)$row->total_purchase_cost, 2, '.', ','),
            number_format((float)$row->fee_in_dollar, 2, '.', ','),
            number_format((float)$row->total_fee, 2, '.', ','),
            number_format((float)$taxAmount, 2, '.', ','),
            number_format((float)$totalFeeWithTax, 2, '.', ',')
        ];
    }

    public function headings(): array
    {
        return [
            "LP",
            "Province",
            "DBA",
            "Retailer",
            "Product",
            "SKU",
            "Category",
            "Brand",
            "Quantity Received",
            "Quantity Sold",
            "Average Price ($)",
            "Opening Inventory Unit",
            "Closing Inventory Unit",
            "Unit Cost ($)",
            "Total Purchase Cost ($)",
            "Fee ($)",
            "Total Payouts ($)",
            "Tax Amount ($)",
            "Total Payouts With Tax ($)",
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        foreach (['O', 'P', 'S', 'T', 'U', 'V', 'W', 'X'] as $col) {
            $sheet->getStyle("{$col}2:{$col}{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
        }

        $lastColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '91B6FE'],
            ],
        ]);

        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }

    private function clearValue($value)
    {
        return is_null($value) ? null : preg_replace('/\s+/', ' ', trim(str_replace(['$', ','], '', $value)));
    }

    private function getAllProvince($province)
    {
        $provinces = [
            'ON' => 'Ontario', 'Ontario' => 'Ontario',
            'MB' => 'Manitoba', 'Manitoba' => 'Manitoba',
            'BC' => 'British Columbia', 'British Columbia' => 'British Columbia',
            'AB' => 'Alberta', 'Alberta' => 'Alberta',
            'SK' => 'Saskatchewan', 'Saskatchewan' => 'Saskatchewan',
        ];
        
        return $provinces[$province] ?? 'Unknown';
    }

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
}
