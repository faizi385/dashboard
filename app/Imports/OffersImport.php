<?php
namespace App\Imports;

use App\Models\Offer;
use App\Models\Province;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OffersImport implements ToModel, WithHeadingRow
{
    protected $lpId;
    protected $source; // Add a property to store the source
    protected $errors = []; // Array to store error messages
    protected $hasCheckedHeaders = false; // Flag to check if headers have been validated

    // Constructor to accept the LP ID and source
    public function __construct($lpId, $source)
    {
        $this->lpId = $lpId;
        $this->source = $source; // Initialize the source
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
      
        $requiredHeaders = [
            'gtin_unit', 'product', 
            'offer_start', 'provincial_sku', 'province', 'data_fee', 
            'category', 'brand', 'thc_range', 'cbd_range'
        ];
    
        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                // Remove underscores from missing headers
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header); // Replace underscores with spaces
                }, $missingHeaders);
    
                // Log an error and throw an exception to stop the import
                $errorMessage = 'Missing headers: ' . implode(', ', $formattedHeaders);
                Log::error($errorMessage);
                $this->errors[] = $errorMessage;
    
                // Throw an exception to stop the import
                throw new \Exception($errorMessage); // Stop the import completely
            }
    
            $this->hasCheckedHeaders = true; // Set the flag to prevent further checks
        }
    
        // Continue processing the row only if all headers are present
        $gtin = (int)($row['gtin_unit'] ?? $row['gtin'] ?? null);
        $productName = $row['product'] ?? $row['product_name'] ?? null;
        $unitCost = $row['unit_cost_excl_hst'] ?? $row['unit_cost'] ?? null;
        $caseQuantity = $row['case_quantity_units_per_case'] ?? $row['case_quantity'] ?? null;
        $offerStart = $row['offer_start'] ?? null;
        
        if ($offerStart === null) {
            throw new \Exception("Offer start date cannot be null");
        }
    
        $unitCost = $unitCost ?? 0;
        $caseQuantity = $caseQuantity ?? 1;
        $productSize = $row['Product Size (g)'] ?? $row['product_size_g'] ?? null;
        $provinceSlug = $row['province'] ?? null;
        $province = Province::where('slug',$provinceSlug)->first();
        $provinceName = $province->name;
        $provinceId = $province->id;
    
        return new Offer([
            'product_name' => $productName,
            'gtin' => $gtin,
            'provincial_sku' => $row['provincial_sku'] ?? null,
            'province' => $provinceName,
            'province_id'=> $provinceId,
            'province_slug' => $provinceSlug,
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
            'lp_id' => $this->lpId,
            'offer_date' => $this->parseDate($row['offer_date'] ?? null),
            'retailer_id' => $row['retailer_id'] ?? null,
            'source' => $this->source,
            'offer_date' => now()->startOfMonth(), 
        ]);
    }
    
    public function getErrors()
    {
        return $this->errors; // Return collected errors
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
            'SK' => 'Saskatchewan',
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
