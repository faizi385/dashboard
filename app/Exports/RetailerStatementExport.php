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
        $quantity_received = (float)($this->clearValue($row->quantity) ?? 0.00);
        $quantity_sold = (float)($this->clearValue($row->quantity_sold) ?? 0.00);
        $average_price = (float)($this->clearValue($row->average_price) ?? 0.00);
        $unit_cost = (float)($this->clearValue($row->unit_cost) ?? 0.00);
        $total_purchase_cost = $quantity_received * $unit_cost;
        $total_payouts = $quantity_sold * $average_price;
        $tax_amount = $total_payouts * 0.13;
        $total_payouts_with_tax = $total_payouts + $tax_amount;
    
        return [
            $row->lp_id ?? 'Unknown LP',
            $this->getAllProvince($row->report->province ?? 'Unknown Province'),
            $this->clearValue($row->report->retailer->dba ?? 'Unknown DBA'),
            trim($this->clearValue($row->report->retailer->first_name ?? '') . ' ' . $this->clearValue($row->report->retailer->last_name ?? '')) ?: 'Unknown Retailer',
            $this->clearValue($row->product_name ?? 'Unknown Product'),  // Updated line for product name
            $this->clearValue($row->sku) ?? 'N/A',
            $this->clearValue($row->category) ?? 'N/A',
            $this->clearValue($row->brand) ?? 'N/A',
            number_format($quantity_received, 2, '.', ','),
            number_format($quantity_sold, 2, '.', ','),
            number_format($average_price, 2, '.', ','),
            number_format($this->clearValue($row->opening_inventory_unit) ?? 0.00, 2, '.', ','),
            number_format($this->clearValue($row->closing_inventory_unit) ?? 0.00, 2, '.', ','),
            number_format($unit_cost, 2, '.', ','),
            number_format($total_purchase_cost, 2, '.', ','),
            number_format($this->clearValue($row->fee_in_dollar) ?? 0.00, 2, '.', ','),
            number_format($total_payouts, 2, '.', ','),
            number_format(($row->province_tax_value * $total_payouts) / 100, 2, '.', ','),
            number_format($total_payouts_with_tax, 2, '.', ','),
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
        $province_name = '';
        switch ($province) {
            case 'ON':
            case 'Ontario':
                $province_name = 'Ontario';
                break;
            case 'MB':
            case 'Manitoba':
                $province_name = 'Manitoba';
                break;
            case 'BC':
            case 'British Columbia':
                $province_name = 'British Columbia';
                break;
            case 'AB':
            case 'Alberta':
                $province_name = 'Alberta';
                break;
            case 'SK':
            case 'Saskatchewan':
                $province_name = 'Saskatchewan';
                break;
            default:
                $province_name = 'Unknown';
        }
        return $province_name;
    }
}
