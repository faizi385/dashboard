<?php

namespace App\Imports;

use App\Models\Offer;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Province;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OffersImport implements ToModel, WithHeadingRow
{
    protected $lpId;
    protected $source;
    protected $lpName;
    protected $errors = [];
    protected $hasCheckedHeaders = false;


    public function __construct($lpId, $source, $lpName)
    {
        $this->lpId = $lpId;
        $this->source = $source;
        $this->lpName = $lpName;
    }


    public function model(array $row)
    {
        $requiredHeaders = [
            'gtin_unit', 'product',
            'offer_start', 'provincial_sku', 'province', 'data_fee',
            'category', 'brand', 'thc_range', 'cbd_range'
        ];

        // Check for required headers only once
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

        // Retrieve and validate data
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

        // Create the Offer model instance
        $offer = new Offer([
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
            'lp_name' => $this->lpName, // Ensure lp_name is correctly assigned
            'offer_date' => now()->startOfMonth()->subMonth(),
            'retailer_id' => $row['retailer_id'] ?? null,
            'source' => $this->source,
        ]);

        // Store the product details
        $this->storeProduct($offer);

        return $offer;
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

    private function storeProduct($data)
    {
        $existingProduct = \App\Models\Product::where('gtin', $data['gtin'])->first();

        if (!$existingProduct) {
            $product = Product::create([
                'product_name' => $data['product_name'],
                'provincial_sku' => $data['provincial_sku'],
                'gtin' => $data['gtin'],
                'province' => $data['province'],
                'province_id' => $data['province_id'], // New field
                'category' => $data['category'],
                'brand' => $data['brand'],
                'lp_id' => $data['lp_id'],
                'product_size' => $data['product_size'],
                'thc_range' => $data['thc_range'],
                'cbd_range' => $data['cbd_range'],
                'comment' => $data['comment'],
                'product_link' => $data['product_link'],
                'unit_cost' => $data['unit_cost'],
            ]);
        } else {
            $product = $existingProduct; // Use the existing product
            $product->update([
                'province_id' => $data['province_id'], // Update province ID if necessary
            ]);
        }

        // Check if the product variation exists based on provincial_sku and gtin
        $existingVariation = \App\Models\ProductVariation::where('provincial_sku', $data['provincial_sku'])
                                    ->where('gtin', $data['gtin'])
                                    ->first();

        if ($existingVariation) {
            if ($existingVariation->province !== $data['province']) {
                // Create a new variation if the province is different
                \App\Models\ProductVariation::create([
                    'product_name' => $data['product_name'],
                    'provincial_sku' => $data['provincial_sku'],
                    'gtin' => $data['gtin'],
                    'province' => $data['province'],
                    'province_id' => $data['province_id'], // New field
                    'category' => $data['category'],
                    'brand' => $data['brand'],
                    'lp_id' => $data['lp_id'],
                    'product_size' => $data['product_size'],
                    'thc_range' => $data['thc_range'],
                    'cbd_range' => $data['cbd_range'],
                    'comment' => $data['comment'],
                    'product_link' => $data['product_link'],
                    'price_per_unit' => $data['unit_cost'],
                    'product_id' => $product->id, // Link to the product
                ]);
            }
            return;
        }

        // Create a new product variation and link it to the product_id
        \App\Models\ProductVariation::create([
            'product_name' => $data['product_name'],
            'provincial_sku' => $data['provincial_sku'],
            'gtin' => $data['gtin'],
            'province' => $data['province'],
            'province_id' => $data['province_id'], // New field
            'category' => $data['category'],
            'brand' => $data['brand'],
            'lp_id' => $data['lp_id'],
            'product_size' => $data['product_size'],
            'thc_range' => $data['thc_range'],
            'cbd_range' => $data['cbd_range'],
            'comment' => $data['comment'],
            'product_link' => $data['product_link'],
            'price_per_unit' => $data['unit_cost'],
            'product_id' => $product->id, // Link to the product
        ]);
    }

}
