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
                    // Store only the fields that changed
                    $description['old'][$key] = $value;
                    $description['new'][$key] = $newValues[$key];
                }
            }
        } elseif ($action === 'created') {
            // For created offers, just log the new values
            $description['new'] = [
                'product_name' => $offer->product_name,
                'provincial_sku' => $offer->provincial_sku,
                'gtin' => $offer->gtin,
                'province' => $offer->province,
                'general_data_fee' => $offer->general_data_fee,
                'exclusive_data_fee' => $offer->exclusive_data_fee,
                'unit_cost' => $offer->unit_cost,
                'category' => $offer->category,
                'brand' => $offer->brand,
                'case_quantity' => $offer->case_quantity,
                'offer_start' => $offer->offer_start,
                'offer_end' => $offer->offer_end,
                'product_size' => $offer->product_size,
                'thc_range' => $offer->thc_range,
                'cbd_range' => $offer->cbd_range,
                'lp_dba' => $lpDba, // Use lp_dba instead of lp_id
                'offer_date' => $offer->offer_date,
                'product_link' => $offer->product_link,
                'comment' => $offer->comment,
            ];
        } elseif ($action === 'deleted') {
            // Log the old values when an offer is deleted
            $description['old'] = [
                'product_name' => $offer->product_name,
                'provincial_sku' => $offer->provincial_sku,
                'gtin' => $offer->gtin,
                'province' => $offer->province,
                'general_data_fee' => $offer->general_data_fee,
                'exclusive_data_fee' => $offer->exclusive_data_fee,
                'unit_cost' => $offer->unit_cost,
                'category' => $offer->category,
                'brand' => $offer->brand,
                'case_quantity' => $offer->case_quantity,
                'offer_start' => $offer->offer_start,
                'offer_end' => $offer->offer_end,
                'product_size' => $offer->product_size,
                'thc_range' => $offer->thc_range,
                'cbd_range' => $offer->cbd_range,
                'lp_dba' => $lpDba, // Use lp_dba instead of lp_id
                'offer_date' => $offer->offer_date,
                'product_link' => $offer->product_link,
                'comment' => $offer->comment,
            ];
        }

        // Create the log entry
        OfferLog::create([
            'offer_id' => $offer->id,
            'user_id' => Auth::id(), // Log the user who made the change
            'action' => $action,
            'description' => json_encode($description), // Store details as JSON
        ]);
    }
}
