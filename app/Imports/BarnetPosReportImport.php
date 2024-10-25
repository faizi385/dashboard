<?php
namespace App\Imports;

use App\Models\BarnetPosReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class BarnetPosReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $errors = []; // Array to store error messages
    protected $hasCheckedHeaders = false; // Flag to check if headers have been validated

    public function __construct($location, $reportId)
    {
        $this->location = $location;
        $this->reportId = $reportId;
    }

    public function model(array $row)
    {
        // List of required headers
        $requiredHeaders = [
            'store', 'product_sku', 'description', 'uom', 'category', 
            'opening_inventory_units', 'opening_inventory_value', 
            'quantity_purchased_units', 'quantity_purchased_value', 
            'returns_from_customers_units', 'returns_from_customers_value', 
            'other_additions_units', 'other_additions_value', 
            'quantity_sold_units', 'quantity_sold_value', 
            'transfer_units', 'transfer_value', 
            'returns_to_vendor_units', 'returns_to_vendor_value', 
            'inventory_adjustment_units', 'inventory_adjustment_value', 
            'destroyed_units', 'destroyed_value', 
            'closing_inventory_units', 'closing_inventory_value', 
            'min_stock', 'low_inv'
        ];

        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                // Remove underscores from missing headers
                $formattedHeaders = array_map(function($header) {
                    return str_replace('_', ' ', $header); // Replace underscores with spaces
                }, $missingHeaders);
        
                // Log an error
                Log::error('Missing headers: ' . implode(', ', $formattedHeaders));
        
                // Throw an exception with a formatted error message
                throw new \Exception('Missing headers: ' . implode(', ', $formattedHeaders));
        
                $this->hasCheckedHeaders = true; // Set the flag to prevent further checks
            }
        }
        
        // Proceed with creating the model if headers are valid
        return new BarnetPosReport([
            'store' => $row['store'] ?? null,
            'product_sku' => $row['product_sku'] ?? null,
            'description' => $row['description'] ?? null,
            'uom' => $row['uom'] ?? null,
            'category' => $row['category'] ?? null,
            'opening_inventory_units' => $row['opening_inventory_units'] ?? null,
            'opening_inventory_value' => $row['opening_inventory_value'] ?? null,
            'quantity_purchased_units' => $row['quantity_purchased_units'] ?? null,
            'quantity_purchased_value' => $row['quantity_purchased_value'] ?? null,
            'returns_from_customers_units' => $row['returns_from_customers_units'] ?? null,
            'returns_from_customers_value' => $row['returns_from_customers_value'] ?? null,
            'other_additions_units' => $row['other_additions_units'] ?? null,
            'other_additions_value' => $row['other_additions_value'] ?? null,
            'quantity_sold_units' => $row['quantity_sold_units'] ?? null,
            'quantity_sold_value' => $row['quantity_sold_value'] ?? null,
            'transfer_units' => $row['transfer_units'] ?? null,
            'transfer_value' => $row['transfer_value'] ?? null,
            'returns_to_vendor_units' => $row['returns_to_vendor_units'] ?? null,
            'returns_to_vendor_value' => $row['returns_to_vendor_value'] ?? null,
            'inventory_adjustment_units' => $row['inventory_adjustment_units'] ?? null,
            'inventory_adjustment_value' => $row['inventory_adjustment_value'] ?? null,
            'destroyed_units' => $row['destroyed_units'] ?? null,
            'destroyed_value' => $row['destroyed_value'] ?? null,
            'closing_inventory_units' => $row['closing_inventory_units'] ?? null,
            'closing_inventory_value' => $row['closing_inventory_value'] ?? null,
            'min_stock' => $row['min_stock'] ?? null,
            'low_inv' => $row['low_inv'] ?? null,
            'report_id' => $this->reportId,
            'location' => $this->location,
        ]);
    }

    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }
}
