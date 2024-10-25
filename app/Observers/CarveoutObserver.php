<?php

namespace App\Observers;

use App\Models\Carveout;
use App\Models\CarveoutLog;
use App\Models\Retailer; 
use App\Models\Lp; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CarveoutObserver
{
    public function created(Carveout $carveout)
    {
        // Fetch the Retailer and LP DBA
        $retailerDba = Retailer::find($carveout->retailer_id)->dba ?? 'N/A';
        $lpDba = Lp::find($carveout->lp_id)->dba ?? 'N/A';

        CarveoutLog::create([
            'carveout_id' => $carveout->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'description' => json_encode([
                'new' => [
                    'province' => $carveout->province,
                    'Retailer Dba' => $retailerDba,
                    'LP Dba' => $lpDba,
                    'location' => $carveout->location,
                    'sku' => $carveout->sku,
                    'date' => Carbon::parse($carveout->date)->format('Y-m-d H:i:s'),
                    'created_at' => Carbon::parse($carveout->created_at)->format('Y-m-d H:i:s'),
                ],
            ]),
        ]);
    }

    public function updated(Carveout $carveout)
    {
        // Fetch the old values before updating
        $oldValues = $carveout->getOriginal(); 
        $newValues = $carveout->getChanges(); 
    
        // Initialize the description array
        $description = [
            'old' => [],
            'new' => [],
        ];
    
        // Loop through the original values to find changes
        foreach ($oldValues as $key => $oldValue) {
            if (array_key_exists($key, $newValues) && $oldValue !== $newValues[$key]) {
                if ($key === 'retailer_id') {
                    $description['old']['Retailer Dba'] = Retailer::find($oldValue)->dba ?? 'N/A';
                    $description['new']['Retailer Dba'] = Retailer::find($newValues[$key])->dba ?? 'N/A';
                } elseif ($key === 'lp_id') {
                    $description['old']['LP Dba'] = Lp::find($oldValue)->dba ?? 'N/A';
                    $description['new']['LP Dba'] = Lp::find($newValues[$key])->dba ?? 'N/A';
                } else {
                    $description['old'][$key] = $oldValue;
                    $description['new'][$key] = $newValues[$key];
                }
            }
        }
    
        // Create the log entry
        CarveoutLog::create([
            'carveout_id' => $carveout->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'description' => json_encode($description),
        ]);
    }

    public function deleted(Carveout $carveout)
    {
        // Fetch the DBA for the deleted carveout
        $retailerDba = Retailer::find($carveout->retailer_id)->dba ?? 'N/A';
        $lpDba = Lp::find($carveout->lp_id)->dba ?? 'N/A';

        CarveoutLog::create([
            'carveout_id' => $carveout->id,
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'description' => json_encode([
                'old' => [
                    'province' => $carveout->province,
                    'Retailer Dba' => $retailerDba,
                    'LP Dba' => $lpDba,
                    'location' => $carveout->location,
                    'sku' => $carveout->sku,
                    'date' => Carbon::parse($carveout->date)->format('Y-m-d H:i:s'),
                ],
            ]),
        ]);
    }
}
