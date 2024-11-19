<?php

namespace App\Imports;

use App\Models\ProfitTechInventoryLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class ProfitTechInventoryLogImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $retailerId; // New property for retailer ID
    protected $lpId;
    protected $errors = []; // Array to store error messages
    protected $hasCheckedHeaders = false; // Flag to ensure headers are only checked once
    protected $requiredHeaders = [
        'product_sku', 'opening_inventory_units', 'opening_inventory_value', 
        'quantity_purchased_units', 'quantity_purchased_value', 'quantity_purchased_units_transfer', 
        'quantity_purchased_value_transfer', 'returns_from_customers_units', 'returns_from_customers_value', 
        'other_additions_units', 'other_additions_value', 'quantity_sold_instore_units', 
        'quantity_sold_instore_value', 'quantity_sold_online_units', 'quantity_sold_online_value', 
        'quantity_sold_units_transfer', 'quantity_sold_value_transfer', 'quantity_destroyed_units', 
        'quantity_destroyed_value', 'quantity_losttheft_units', 'quantity_losttheft_value', 
        'returns_to_aglc_units', 'returns_to_aglc_value', 'other_reductions_units', 'other_reductions_value', 
        'closing_inventory_units', 'closing_inventory_value'
    ];

    public function __construct($location, $reportId, $retailerId, $lpId = null)
    {
        $this->location = $location;
        $this->reportId = $reportId;
        $this->retailerId = $retailerId; // Assign retailer ID
        $this->lpId = $lpId;  
    }

    public function model(array $row)
    {
        // Check if required headers are missing
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($this->requiredHeaders, array_keys($row));

            if (!empty($missingHeaders)) {
                // Convert underscores to spaces for readability
                $formattedMissingHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header);
                }, $missingHeaders);

                // Log the error and add to the errors array
                Log::error('Missing headers: ' . implode(', ', $formattedMissingHeaders));
                $this->errors[] = 'Missing headers: ' . implode(', ', $formattedMissingHeaders);

                // Set the flag to prevent further checks
                $this->hasCheckedHeaders = true;

                // Throw an exception to stop the import process
                throw new \Exception('' . implode(', ', $this->errors));
            }

            // Set the flag to prevent further header checks
            $this->hasCheckedHeaders = true;
        }

        // Proceed with creating the model if headers are valid
        return new ProfitTechInventoryLog([
            'location' => $this->location,
            'report_id' => $this->reportId,
            'product_sku' => $row['product_sku'] ?? null,
            'opening_inventory_units' => $row['opening_inventory_units'] ?? null,
            'opening_inventory_value' => $row['opening_inventory_value'] ?? null,
            'quantity_purchased_units' => $row['quantity_purchased_units'] ?? null,
            'quantity_purchased_value' => $row['quantity_purchased_value'] ?? null,
            'quantity_purchased_units_transfer' => $row['quantity_purchased_units_transfer'] ?? null,
            'quantity_purchased_value_transfer' => $row['quantity_purchased_value_transfer'] ?? null,
            'returns_from_customers_units' => $row['returns_from_customers_units'] ?? null,
            'returns_from_customers_value' => $row['returns_from_customers_value'] ?? null,
            'other_additions_units' => $row['other_additions_units'] ?? null,
            'other_additions_value' => $row['other_additions_value'] ?? null,
            'quantity_sold_instore_units' => $row['quantity_sold_instore_units'] ?? null,
            'quantity_sold_instore_value' => $row['quantity_sold_instore_value'] ?? null,
            'quantity_sold_online_units' => $row['quantity_sold_online_units'] ?? null,
            'quantity_sold_online_value' => $row['quantity_sold_online_value'] ?? null,
            'quantity_sold_units_transfer' => $row['quantity_sold_units_transfer'] ?? null,
            'quantity_sold_value_transfer' => $row['quantity_sold_value_transfer'] ?? null,
            'quantity_destroyed_units' => $row['quantity_destroyed_units'] ?? null,
            'quantity_destroyed_value' => $row['quantity_destroyed_value'] ?? null,
            'quantity_losttheft_units' => $row['quantity_losttheft_units'] ?? null,
            'quantity_losttheft_value' => $row['quantity_losttheft_value'] ?? null,
            'returns_to_aglc_units' => $row['returns_to_aglc_units'] ?? null,
            'returns_to_aglc_value' => $row['returns_to_aglc_value'] ?? null,
            'other_reductions_units' => $row['other_reductions_units'] ?? null,
            'other_reductions_value' => $row['other_reductions_value'] ?? null,
            'closing_inventory_units' => $row['closing_inventory_units'] ?? null,
            'closing_inventory_value' => $row['closing_inventory_value'] ?? null,
            'retailer_id' => $this->retailerId, // Include retailer ID
            'lp_id' => $this->lpId,
        ]);
    }

    // Get the errors collected during the import process
    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }
}
