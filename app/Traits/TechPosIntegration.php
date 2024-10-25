<?php

namespace App\Traits;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\TechPOSReport; // Assuming this is your TechPOS report model
use Illuminate\Support\Facades\Log;

trait TechPOSIntegration
{
    use ICIntegrationTrait;

    /**
     * Process TechPOS reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function processTechPOSReports($reports)
    {
        Log::info('Processing TechPOS reports:', ['reports' => $reports]);

        foreach ($reports as $report) {
            // Retrieve the TechPOS report by report_id
            $techPOSReport = TechPOSReport::with('report')->find($report->id);

            if (!$techPOSReport) {
                Log::warning('TechPOS report not found:', ['report_id' => $report->id]);
                continue;
            }

            $retailer_id = $techPOSReport->report->retailer_id ?? null;
            $location = $techPOSReport->report->location ?? null;

            if (!$retailer_id) {
                Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
                continue;
            }

            $sku = $techPOSReport->sku;
            $gtin = $techPOSReport->barcode;

            $provinceName = null;
            $provinceSlug = null;
            $product = null;
            $lpName = null; // Initialize LP Name variable
            $retailerName = null; // Initialize Retailer Name variable

            // Fetch Retailer Name
            $retailer = Retailer::find($retailer_id);
            if ($retailer) {
                $retailerName = trim("{$retailer->first_name} {$retailer->last_name}");
            } else {
                Log::warning('Retailer not found:', ['retailer_id' => $retailer_id]);
            }

            // Match the product using SKU and GTIN
            if (!empty($sku)) {
                $product = $this->matchICSku($sku);
            } elseif (!empty($techPOSReport->productname)) {
                // If no SKU match, try to match product by name
                $product = $this->matchICProductName($techPOSReport->productname);
            }
            if ($product) {
                // Fetch province information
                $provinceName = $product->province;
                $province = Province::where('name', $provinceName)->first();
                $provinceSlug = $province->slug ?? null;

                // Fetch LP name using lp_id
                $lpName = Product::find($product->id)->lp->name ?? null; // Assuming you have a relationship set up in Product model
                
                // Calculate dqi_fee and dqi_per
                $dqi_fee = $this->calculateDqiFee($techPOSReport, $product);
                $dqi_per = $this->calculateDqiPer($techPOSReport, $product);

                $cleanSheetData = [
                    'retailer_id' => $retailer_id,
                    'lp_id' => $product->lp_id,
                    'report_id' => $report->id,
                    'retailer_name' => $retailerName, // Use fetched Retailer name
                    'lp_name' => $lpName, // Use fetched LP name
                    'thc_range' => $product->thc_range,
                    'cbd_range' => $product->cbd_range,
                    'size_in_gram' => $product->size_in_gram,
                    'location' => $location,
                    'province' => $provinceName,
                    'province_slug' => $provinceSlug,
                    'sku' => $sku,
                    'product_name' =>  $techPOSReport->productname,
                    'category' => $product->category,
                    'brand' => $product->brand,
                    'sold' => $techPOSReport->quantitysoldunits ?? '0',
                    'purchase' => $techPOSReport->quantitypurchasedunits ?? '0',
                    'average_price' => $report->average_price,
                    'average_cost' => $report->average_cost,
                    'report_price_og' => $report->report_price_og,
                    'barcode' => $gtin,
                    'transfer_in' => $report->transfer_in,
                    'transfer_out' => $report->transfer_out,
                    'pos' => 'TechPOS',
                    'pos_report_id' => $techPOSReport->id,
                    'comment' => 'Record found in the Master Catalog',
                    'opening_inventory_unit' => $techPOSReport->openinventoryunits ?? '0',
                    'closing_inventory_unit' => $techPOSReport->closinginventoryunits ?? '0',
                    'purchase' => $techPOSReport->purchased ?? '0',
                    'dqi_fee' => $dqi_fee,
                    'dqi_per' => $dqi_per,
                    'reconciliation_date' => now(),
                ];

                $this->saveToCleanSheet($cleanSheetData);
            } else {
                Log::warning('Product not found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin, 'report_data' => $report]);

                // Match the offer using SKU and GTIN
                $offer = !empty($sku) ? $this->matchOfferSku($sku) : null;
             

                if (!$offer && !empty($techPOSReport->productname)) {
                    $offer = $this->matchOfferProductName($techPOSReport->productname);
                }

                if ($offer) {
                    // Fetch LP name using lp_id
                    $lpName = Offer::find($offer->id)->lp->name ?? null;

                    // Handle offer data if product not found
                    $dqi_fee = $this->calculateDqiFee($techPOSReport, $offer);
                    $dqi_per = $this->calculateDqiPer($techPOSReport, $offer);

                    $cleanSheetData = [
                        'retailer_id' => $retailer_id,
                        'lp_id' => $offer->lp_id,
                        'report_id' => $report->id,
                        'retailer_name' => $retailerName,
                        'lp_name' => $lpName,
                        'thc_range' => $offer->thc_range,
                        'cbd_range' => $offer->cbd_range,
                        'size_in_gram' => $offer->size_in_gram,
                        'location' => $location,
                        'province' => $offer->province,
                        'province_slug' => $offer->province_slug,
                        'sku' => $sku,
                        'product_name' => $offer->product_name,
                        'category' => $offer->category,
                        'brand' => $offer->brand,
                        'sold' => $techPOSReport->quantitysoldunits ?? '0',
                        'purchase' =>$techPOSReport->quantitypurchasedunits ?? '0',
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'TechPOS',
                        'pos_report_id' => $techPOSReport->id,
                        'comment' => 'Record found in the Offers Table',
                        'opening_inventory_unit' => $techPOSReport->openinventoryunits ?? '0',
                        'closing_inventory_unit' =>$techPOSReport->closinginventoryunits ?? '0',
                        'purchase' => $techPOSReport->purchased ?? '0',
                        'dqi_fee' => $dqi_fee,
                        'dqi_per' => $dqi_per,
                        'reconciliation_date' => now(),
                    ];

                    $this->saveToCleanSheet($cleanSheetData);
                } else {
                    Log::info('No product or offer found, saving report data as is:', ['report_data' => $report]);

                    $cleanSheetData = [
                        'retailer_id' => $retailer_id,
                        'lp_id' => null,
                        'report_id' => $report->id,
                        'retailer_name' => $retailerName,
                        'lp_name' => $lpName,
                        'thc_range' => null,
                        'cbd_range' => null,
                        'size_in_gram' => null,
                        'location' => $location,
                        'province' => null,
                        'province_slug' => null,
                        'sku' => $techPOSReport->$sku,
                        'product_name' => $techPOSReport->productname,
                        'category' => $techPOSReport->category,
                        'brand' => $techPOSReport->brand,
                        'sold' => $techPOSReport->quantitysoldunits ?? '0',
                        'purchase' =>$techPOSReport->quantitypurchasedunits ?? '0',
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'TechPOS',
                        'pos_report_id' => $techPOSReport->id,
                        'comment' => 'No match found, saved report as is',
                        'opening_inventory_unit' => $techPOSReport->openinventoryunits ?? '0',
                        'closing_inventory_unit' => $techPOSReport->closinginventoryunits ?? '0',
                        'purchase' => $techPOSReport->purchased ?? '0',
                        'dqi_fee' => null,
                        'dqi_per' => null,
                        'reconciliation_date' => now(),
                    ];

                    $this->saveToCleanSheet($cleanSheetData);
                }
            }
        }

        Log::info('Finished processing TechPOS reports.');
    }
}
