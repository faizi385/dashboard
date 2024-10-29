<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\Offer;
use App\Models\CleanSheet;
use Illuminate\Support\Facades\Log;

trait ICIntegrationTrait
{
    public function saveToCleanSheet(array $cleanSheetData)
    {
        try {
            CleanSheet::create($cleanSheetData);
            Log::info('Data saved to CleanSheet successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving data to CleanSheet:', ['error' => $e->getMessage()]);
        }
    }

    public function matchICBarcode(string $barcode)
    {
        $product = Product::where('gtin', $barcode)->first();

        if ($product) {
            Log::info('Product matched by barcode:', ['barcode' => $barcode, 'product_id' => $product->id]);
            return $product;
        }

        Log::warning('No product found for barcode:', ['barcode' => $barcode]);
        return null;
    }

    public function matchICSku(string $sku)
    {
        $product = Product::where('provincial_sku', $sku)->first();

        if ($product) {
            Log::info('Product matched by SKU:', ['sku' => $sku, 'product_id' => $product->id]);
            return $product;
        }

        Log::warning('No product found for SKU:', ['sku' => $sku]);
        return null;
    }

    public function matchICBarcodeSku(string $sku, string $gtin)
    {
        $product = Product::where('provincial_sku', $sku)
            ->where('gtin', $gtin)
            ->first();

        if ($product) {
            Log::info('Product matched by SKU and GTIN:', [
                'sku' => $sku,
                'gtin' => $gtin,
                'product_id' => $product->id
            ]);
            return $product;
        }

        Log::warning('No product found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin]);
        return null;
    }

    public function matchOfferBarcode(string $gtin)
    {
        $offer = Offer::where('gtin', $gtin)->first();

        if ($offer) {
            Log::info('Offer matched by GTIN:', ['gtin' => $gtin, 'offer_id' => $offer->id]);
            return $offer;
        }

        Log::warning('No offer found for GTIN:', ['gtin' => $gtin]);
        return null;
    }

    public function matchOfferSku(string $sku)
    {
        $offer = Offer::where('provincial_sku', $sku)->first();

        if ($offer) {
            Log::info('Offer matched by SKU:', ['sku' => $sku, 'offer_id' => $offer->id]);
            return $offer;
        }

        Log::warning('No offer found for SKU:', ['sku' => $sku]);
        return null;
    }

    public function matchOfferProduct(string $sku, string $gtin)
    {
        $offer = Offer::where('provincial_sku', $sku)
            ->orWhere('gtin', $gtin)
            ->first();

        if ($offer) {
            Log::info('Offer matched by SKU and GTIN:', [
                'sku' => $sku,
                'gtin' => $gtin,
                'offer_id' => $offer->id
            ]);
            return $offer;
        }

        Log::warning('No offer found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin]);
        return null;
    }

    protected function matchICProductName($productName)
    {
        return Product::where('product_name', 'LIKE', "%{$productName}%")->first();
    }

    public function matchOfferProductName($productName)
    {
        return Offer::where('product_name', 'LIKE', '%' . $productName . '%')->first();
    }

    protected function calculateDqiFee($greenlineReport, $item)
    {
        return ($greenlineReport->sold ?? 0) * ($item->average_cost ?? 0);
    }

    protected function calculateDqiPer($greenlineReport, $item)
    {
        return ($greenlineReport->sold ?? 0) / ($item->average_price ?? 1);
    }

    public function DQISummaryFlag($sku = null, $gtin = null)
    {
        $offer = null;

        if (!empty($gtin) && !empty($sku)) {
            $offer = $this->matchOfferProduct($sku, $gtin); 
        } elseif (!empty($gtin)) {
            $offer = $this->matchOfferBarcode($gtin); 
        } elseif (!empty($sku)) {
            $offer = $this->matchOfferSku($sku); 
        }

        return $offer;
    }
}
