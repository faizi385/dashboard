<?php

namespace App\Imports;

use App\Models\Offer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // If your Excel has headers
use Carbon\Carbon;

class OffersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    { dd($row);
        return new Offer([
            'product_name' => $row['product_name'] ?? null,
            'provincial_sku' => $row['provincial_sku'] ?? null,
            'gtin' => $row['gtin'] ?? null,
            'province' => $row['province'] ?? null,
            'data_fee' => $this->convertToFloat($row['data_fee']), // Ensure data_fee is a float
            'unit_cost' => $this->convertToFloat($row['unit_cost']), // Ensure unit_cost is a float
            'category' => $row['category'] ?? null,
            'brand' => $row['brand'] ?? null,
            'case_quantity' => $row['case_quantity'] ?? null,
            'offer_start' => $this->parseDate($row['offer_start']),
            'offer_end' => $this->parseDate($row['offer_end']),
            'product_size' => $row['product_size'] ?? null,
            'thc_range' => $row['thc_range'] ?? null,
            'cbd_range' => $row['cbd_range'] ?? null,
            'comment' => $row['comment'] ?? null,
            'product_link' => $row['product_link'] ?? null,
            'lp_id' => $row['lp_id'] ?? null,
            'offer_date' => $this->parseDate($row['offer_date']),
            'retailer_id' => $row['retailer_id'] ?? null,
        ]);
    }

    /**
     * Helper method to convert a string to float.
     *
     * @param string|null $value
     * @return float|null
     */
    private function convertToFloat($value)
    {
        if (is_numeric($value)) {
            return (float) $value; // Convert to float if numeric
        }
        // Handle non-numeric values, e.g., log error, return null, etc.
        return null; // or throw an exception
    }

    /**
     * Helper method to parse dates.
     *
     * @param string|null $date
     * @return Carbon|null
     */
    private function parseDate($date)
    {
        try {
            return $date ? Carbon::createFromFormat('Y-m-d', $date) : null;
        } catch (\Exception $e) {
            // Log the error or handle it as necessary
            return null; // or throw an exception
        }
    }
}
