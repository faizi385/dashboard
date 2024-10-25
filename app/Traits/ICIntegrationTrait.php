<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\Offer; // Import Offer model
use App\Models\CleanSheet;
use Illuminate\Support\Facades\Log;

trait ICIntegrationTrait
{
    /**
     * Save data to the CleanSheet table.
     *
     * @param array $cleanSheetData
     * @return void
     */
    public function saveToCleanSheet(array $cleanSheetData)
    {
        try {
            CleanSheet::create($cleanSheetData);
            Log::info('Data saved to CleanSheet successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving data to CleanSheet:', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Match product using barcode.
     *
     * @param string $barcode
     * @return Product|null
     */
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

    /**
     * Match product using SKU.
     *
     * @param string $sku
     * @return Product|null
     */
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

    /**
     * Match product using both SKU and GTIN.
     *
     * @param string $sku
     * @param string $gtin
     * @return Product|null
     */
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

    /**
     * Match offer using barcode.
     *
     * @param string $barcode
     * @return Offer|null
     */
    public function matchOfferBarcode(string $barcode)
    {
        $offer = Offer::where('barcode', $barcode)->first();

        if ($offer) {
            Log::info('Offer matched by barcode:', ['barcode' => $barcode, 'offer_id' => $offer->id]);
            return $offer;
        }

        Log::warning('No offer found for barcode:', ['barcode' => $barcode]);
        return null;
    }

    /**
     * Match offer using SKU.
     *
     * @param string $sku
     * @return Offer|null
     */
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

    /**
     * Match offer using both SKU and GTIN.
     *
     * @param string $sku
     * @param string $gtin
     * @return Offer|null
     */
    public function matchOfferProduct(string $sku, string $gtin)
    {
        $offer = Offer::where('provincial_sku', $sku)
            ->orWhere('barcode', $gtin)
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
    // Attempt to find an offer with the specified product name
    return Offer::where('product_name', 'LIKE', '%' . $productName . '%')->first();
}
      /**
     * Calculate the dqi_fee based on the greenline report and product/offer.
     *
     * @param GreenlineReport $greenlineReport
     * @param Product|Offer $item
     * @return float
     */
    protected function calculateDqiFee($greenlineReport, $item)
    {
        // Placeholder calculation, replace with your own logic
        return ($greenlineReport->sold ?? 0) * ($item->average_cost ?? 0);
    }

    /**
     * Calculate the dqi_per based on the greenline report and product/offer.
     *
     * @param GreenlineReport $greenlineReport
     * @param Product|Offer $item
     * @return float
     */
    protected function calculateDqiPer($greenlineReport, $item)
    {
        // Placeholder calculation, replace with your own logic
        return ($greenlineReport->sold ?? 0) / ($item->average_price ?? 1); // Avoid division by zero
    }

}
