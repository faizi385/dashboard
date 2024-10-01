<?php
namespace App\Imports;

use App\Models\Offer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class OffersImport implements ToModel, WithHeadingRow
{
    protected $lpId;

    // Constructor to accept the LP ID
    public function __construct($lpId)
    {
        $this->lpId = $lpId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Handle both 'GTIN (unit)' and 'gtin' headers
        $gtin = (int)$row['gtin_unit'] ?? (int)$row['gtin'] ?? null;

        // Handle both 'product' and 'product_name' headers
        $productName = $row['product'] ?? $row['product_name'] ?? null;

        // Handle both 'Unit Cost (excl. HST)' and 'unit_cost' headers
        $unitCost = $row['unit_cost_excl_hst'] ?? $row['unit_cost'] ?? null;

        // Handle both 'Case Quantity (Units per case)' and 'case_quantity' headers
        $caseQuantity = $row['case_quantity_units_per_case'] ?? $row['case_quantity'] ?? null;

        // Handle 'Offer Start' header
        $offerStart = $row['offer_start'] ?? null;
        if ($offerStart === null) {
            throw new \Exception("Offer start date cannot be null");
        }

        // Default values for unit_cost and case_quantity
        $unitCost = $unitCost ?? 0;
        $caseQuantity = $caseQuantity ?? 1;

        // Handle 'Product Size' header
        $productSize = $row['Product Size (g)'] ?? $row['product_size'] ?? null;

        // Handle province and province_slug
        $provinceSlug = $row['province'] ?? null; // Get the province abbreviation from the row
        $province = $this->getFullProvinceName($provinceSlug); // Get the full province name based on the abbreviation

        return new Offer([
            'product_name' => $productName,
            'gtin' => $gtin,
            'provincial_sku' => $row['provincial_sku'] ?? null,
            'province' => $province, // Save full province name
            'province_slug' => $provinceSlug, // Save province abbreviation
            'data_fee' => $this->convertToFloat($row['data_fee'] ?? null),
            'unit_cost' => $this->convertToFloat($unitCost),
            'category' => $row['category'] ?? null,
            'brand' => $row['brand'] ?? null,
            'case_quantity' => $this->convertToInteger($caseQuantity),
            'offer_start' => $this->parseDate($offerStart),
            'product_size' => $productSize,
            'thc_range' => $row['thc_range'] ?? null,
            'cbd_range' => $row['cbd_range'] ?? null,
            'comment' => $row['comment'] ?? null,
            'product_link' => $row['product_link'] ?? null,
            'lp_id' => $this->lpId, // Use the lpId passed to the constructor
            'offer_date' => $this->parseDate($row['offer_date'] ?? null),
            'retailer_id' => $row['retailer_id'] ?? null,
        ]);
    }

    /**
     * Convert province abbreviation to full name.
     */
    private function getFullProvinceName($slug)
    {
        $provinces = [
            'ON' => 'Ontario',
            'AB' => 'Alberta',
            'BC' => 'British Columbia',
            'SK' => 'shinchin',
        ];

        return $provinces[$slug] ?? null;
    }

    private function convertToFloat($value)
    {
        return $value !== null ? (float)$value : null;
    }

    /**
     * Convert value to integer if it's not null.
     */
    private function convertToInteger($value)
    {
        return $value !== null ? (int)$value : null;
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate($date)
    {
        if (!$date) {
            return null;
        }

        // Normalize the date string by adding the year if it's missing
        if (strlen($date) <= 7) {
            // Assuming the year is the current year if not specified
            $date .= '-' . date('Y');
        }

        // Possible date formats
        $formats = [
            'd-M-Y',      // e.g. 01-Aug-2024
            'd-M',        // e.g. 01-Aug
            'd/m/Y',      // e.g. 26/09/2024
            'm/d/Y',      // e.g. 09/26/2024
            'Y-m-d',      // e.g. 2024-09-26
            'Y-m-d H:i:s' // e.g. 2024-09-26 08:37:41
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $date);
            } catch (\Exception $e) {
                // Continue to next format if parsing fails
            }
        }

        return null; // If no formats match
    }
}
