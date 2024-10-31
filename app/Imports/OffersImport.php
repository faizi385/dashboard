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
    protected $lpName; // Add a property to store lp_name
    protected $source;
    protected $errors = [];
    protected $hasCheckedHeaders = false;

    // Constructor to accept the LP ID, LP Name, and source
    public function __construct($lpId, $lpName, $source)
    {
        $this->lpId = $lpId;
        $this->lpName = $lpName; // Initialize lp_name
        $this->source = $source;
    }

    public function model(array $row)
    {
        $requiredHeaders = [
            'gtin_unit', 'product', 
            'offer_start', 'provincial_sku', 'province', 'data_fee', 
            'category', 'brand', 'thc_range', 'cbd_range'
        ];

        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                $formattedHeaders = array_map(fn($header) => str_replace('_', ' ', $header), $missingHeaders);
                $errorMessage = 'Missing headers: ' . implode(', ', $formattedHeaders);
                Log::error($errorMessage);
                $this->errors[] = $errorMessage;
                throw new \Exception($errorMessage);
            }
            $this->hasCheckedHeaders = true;
        }

        $gtin = (int)($row['gtin_unit'] ?? $row['gtin'] ?? null);
        $productName = $row['product'] ?? $row['product_name'] ?? null;
        $unitCost = $row['unit_cost_excl_hst'] ?? $row['unit_cost'] ?? 0;
        $caseQuantity = $row['case_quantity_units_per_case'] ?? $row['case_quantity'] ?? 1;
        $offerStart = $row['offer_start'] ?? null;

        if ($offerStart === null) {
            throw new \Exception("Offer start date cannot be null");
        }

        $productSize = $row['Product Size (g)'] ?? $row['product_size_g'] ?? null;
        $provinceSlug = $row['province'] ?? null;
        $province = Province::where('slug', $provinceSlug)->first();
        $provinceName = $province->name ?? null;
        $provinceId = $province->id ?? null;

        return new Offer([
            'product_name' => $productName,
            'gtin' => $gtin,
            'provincial_sku' => $row['provincial_sku'] ?? null,
            'province' => $provinceName,
            'province_id' => $provinceId,
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
            'lp_name' => $this->lpName, // Add lp_name here
            'offer_date' => now()->startOfMonth(),
            'retailer_id' => $row['retailer_id'] ?? null,
            'source' => $this->source,
        ]);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function convertToFloat($value)
    {
        return $value !== null ? (float)$value : null;
    }

    private function convertToInteger($value)
    {
        return $value !== null ? (int)$value : null;
    }

    private function parseDate($date)
    {
        if (!$date) {
            return null;
        }

        if (strlen($date) <= 7) {
            $date .= '-' . date('Y');
        }

        $formats = [
            'd-M-Y', 'd-M', 'd/m/Y', 'm/d/Y', 'Y-m-d', 'Y-m-d H:i:s'
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $date);
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }
}
