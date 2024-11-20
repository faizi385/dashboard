<?php

namespace App\Exports;

use App\Models\CleanSheet;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CleanSheetsExport implements FromCollection, WithMapping, WithHeadings, WithStyles
{
    private $report_id;
    private $id;

    public function __construct($report_id)
    {
        $this->report_id = $report_id;
        $this->id = $report_id;
    }
    public function collection()
    {
        $export = CleanSheet::with('report.retailer', 'product_variations')->where('report_id', $this->id)->get();
        return $export;
    }

  public function map($row): array
{
    $sold = $this->clearValue($row->sold);
    $average_price =  (float)$this->clearValue($row->average_price);
    $average_cost =  (float)$this->clearValue($row->average_cost);
    $open_inventory =  (float)$this->clearValue($row->opening_inventory_unit) * $average_cost;
    $closing_inventory =  (float)$this->clearValue($row->closing_inventory_unit) * $average_cost;
    $purchases =  (float)$this->clearValue($row->purchase) * $average_cost;
    $sales_at_cost =  (float)$this->clearValue($row->sold) * $average_cost;
    $sales_at_retail =  (float)$this->clearValue($row->sold) * $average_price;

    return [
        $this->clearValue($row->report->retailer->dba) ?? '',
        $this->clearValue($row->report->location) ?? '',
        $row->product_variations ? $this->clearValue($row->product_variations->province) : $this->clearValue($this->get_province($row->report->province)),
        $row->product_variations ? $this->clearValue($row->product_variations->sku) : $this->clearValue($row->sku),
        $row->barcode ? $this->clearValue($row->barcode) : ($row->product_variations ? $this->clearValue($row->product_variations->gtin) : ''),
        $row->product_variations ? $this->clearValue($row->product_variations->product_name) : $this->clearValue($row->product_name),
        $row->size_in_gram ? $this->clearValue($row->size_in_gram) : ($row->product_variations ? $this->clearValue($row->product_variations->product_size) : ''),
        $row->thc_range ? $this->clearValue($row->thc_range) : ($row->product_variations ? $this->clearValue($row->product_variations->thc_range) : ''),
        $row->cbd_range ? $this->clearValue($row->cbd_range) : ($row->product_variations ? $this->clearValue($row->product_variations->cbd_range) : ''),
        $row->lp_name ? $this->clearValue($row->lp_name) : ($row->product_variations ? $this->clearValue($row->product_variations->lp_name) : ''),
        $row->brand ? $this->clearValue($row->brand) : ($row->product_variations ? $this->clearValue($row->product_variations->brand) : ''),
        $row->category ? $this->clearValue($row->category) : ($row->product_variations ? $this->clearValue($row->product_variations->category) : ''),
        $sold ?? 0,
        $this->clearValue($row->purchase),
        $this->clearValue($average_price),
        $this->clearValue($average_cost),
        $this->clearValue($row->opening_inventory_unit),
        $this->clearValue($row->closing_inventory_unit),
        $this->clearValue($open_inventory),
        $this->clearValue($closing_inventory),
        $this->clearValue($purchases),
        $this->clearValue($sales_at_cost),
        $this->clearValue($sales_at_retail),
        ($sales_at_retail != 0) ? $this->clearValue(number_format((($sales_at_retail - $sales_at_cost) / $sales_at_retail) * 100, 2)) : 1,
        ($sales_at_retail != 0) ? $this->clearValue((($sales_at_retail - $sales_at_cost) * $sold)) : 1,
        isset($row->dqi_flag) ? ($row->dqi_flag ? 'Yes' : 'No') : 'No',
        $row->c_flag,
        $this->clearValue($row->dqi_per) ?? 0,
        $this->clearValue($row->dqi_fee) ?? 0,
    ];
}


    public function headings(): array
    {
        return [
            "DBA",
            "Location",
            "Province",
            'SKU',
            "GTIN",
            "Product Name",
            "Size in Grams (g)",
            "THC Range",
            "CBD Range",
            "LP",
            "Brand",
            "Category",
            "Sold",
            "purchase",
            "Average Price ($)",
            "Average Cost ($)",
            "Opening Inventory Unit",
            "Closing Inventory Unit",
            "Opening Inventory ($)",
            "Closing Inventory ($)",
            "Purchases ($)",
            "Sales at Cost ($)",
            "Sales at Retail ($)",
            "Margin (%)",
            "Gross Profit ($)",
            "DQI Purchase",
            "Carveout Flag",
            "DQI Per (%)",
            "DQI Fee ($)",

        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle("O2:O{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("P2:P{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("S2:S{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("T2:T{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("U2:U{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("V2:V{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("W2:W{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("AC2:AC{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("X2:X{$lastRow}")->getNumberFormat()->setFormatCode('0.00\%');
        $sheet->getStyle("AB2:AB{$lastRow}")->getNumberFormat()->setFormatCode('0.00\%');


        $lastColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '91B6FE'],
            ],
        ]);
        $signstyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
        ];
        $signstyleLeft = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ];
//        $sheet->getStyle('J')->applyFromArray($signstyle);
//        $sheet->getStyle('E')->applyFromArray($signstyleLeft);

//        foreach(range('A', $sheet->getHighestDataColumn()) as $column) {
//            $sheet->getColumnDimension($column)->setAutoSize(true);
//        }
        foreach(range('Z', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }

    private function clearValue($value)
    {
        return preg_replace('/\s+/', ' ', trim(str_replace(['$', ','], '', $value)));
    }

    private function get_province($province)
    {
        $province_name = '';
        if ($province == 'ON' || $province == 'Ontario') {
            $province_name = 'Ontario';
        } elseif ($province == 'MB' || $province == 'Manitoba') {
            $province_name = 'Manitoba';
        } elseif ($province == 'BC' || $province == 'British Columbia') {
            $province_name = 'British Columbia';
        } elseif ($province == 'AB' || $province == 'Alberta') {
            $province_name = 'Alberta';
        } elseif ($province == 'SK' || $province == 'Saskatchewan') {
            $province_name = 'Saskatchewan';
        }
        return $province_name;
    }
}
