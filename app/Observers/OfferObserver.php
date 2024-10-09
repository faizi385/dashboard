<?php
namespace App\Observers;

use App\Models\Offer;
use App\Models\OfferLog;
use Illuminate\Support\Facades\Auth;

class OfferObserver
{
    public function created(Offer $offer)
    {
        $this->logOfferChange($offer, 'created');
    }

    public function updated(Offer $offer)
    {
        $this->logOfferChange($offer, 'updated');
    }

    public function deleted(Offer $offer)
    {
        $this->logOfferChange($offer, 'deleted');
    }

    protected function logOfferChange(Offer $offer, string $action)
    {
        // Create an array to hold both old and new values
        $description = [];

        // Fetch the LP DBA if available
        $lpDba = $offer->lp ? $offer->lp->dba : 'N/A';

        // Map database field names to readable labels
        $fieldsMap = [
            'product_name' => 'Product Name',
            'provincial_sku' => 'Provincial SKU',
            'gtin' => 'GTIN',
            'province' => 'Province',
            'general_data_fee' => 'General Data Fee',
            'exclusive_data_fee' => 'Exclusive Data Fee',
            'unit_cost' => 'Unit Cost',
            'category' => 'Category',
            'brand' => 'Brand',
            'case_quantity' => 'Case Quantity',
            'offer_start' => 'Offer Start',
            'offer_end' => 'Offer End',
            'product_size' => 'Product Size',
            'thc_range' => 'THC Range',
            'cbd_range' => 'CBD Range',
            'lp_dba' => 'LP DBA',
            'offer_date' => 'Offer Date',
            'product_link' => 'Product Link',
            'comment' => 'Comment',
        ];

        if ($action === 'updated') {
            // Get the old values before the update
            $oldValues = $offer->getOriginal();
            $newValues = $offer->getChanges();

            // Prepare the description for the updated offer
            $description = [
                'old' => [],
                'new' => [],
            ];

            foreach ($oldValues as $key => $value) {
                if (isset($newValues[$key]) && $value !== $newValues[$key]) {
                    // Map the field names to their labels
                    $label = $fieldsMap[$key] ?? $key;
                    $description['old'][$label] = $value;
                    $description['new'][$label] = $newValues[$key];
                }
            }
        } elseif ($action === 'created') {
            // For created offers, just log the new values
            $description['new'] = $this->mapFields($offer, $fieldsMap);
        } elseif ($action === 'deleted') {
            // Log the old values when an offer is deleted
            $description['old'] = $this->mapFields($offer, $fieldsMap);
        }

        // Create the log entry
        OfferLog::create([
            'offer_id' => $offer->id,
            'user_id' => Auth::id(), // Log the user who made the change
            'action' => $action,
            'description' => json_encode($description), // Store details as JSON
        ]);
    }

    protected function mapFields(Offer $offer, array $fieldsMap)
    {
        // Map the offer fields to human-readable labels
        return [
            'Product Name' => $offer->product_name,
            'Provincial SKU' => $offer->provincial_sku,
            'GTIN' => $offer->gtin,
            'Province' => $offer->province,
            'General Data Fee' => $offer->general_data_fee,
            'Exclusive Data Fee' => $offer->exclusive_data_fee,
            'Unit Cost' => $offer->unit_cost,
            'Category' => $offer->category,
            'Brand' => $offer->brand,
            'Case Quantity' => $offer->case_quantity,
            'Offer Start' => $offer->offer_start,
            'Offer End' => $offer->offer_end,
            'Product Size' => $offer->product_size,
            'THC Range' => $offer->thc_range,
            'CBD Range' => $offer->cbd_range,
            'LP DBA' => $offer->lp ? $offer->lp->dba : 'N/A',
            'Offer Date' => $offer->offer_date,
            'Product Link' => $offer->product_link,
            'Comment' => $offer->comment,
        ];
    }
}
