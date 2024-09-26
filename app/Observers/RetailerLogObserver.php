<?php
namespace App\Observers;

use App\Models\Retailer;
use App\Models\RetailerLog;
use Illuminate\Support\Facades\Auth;

class RetailerLogObserver
{
    public function created(Retailer $retailer)
    {
        $this->log('created', $retailer);
    }

    public function updated(Retailer $retailer)
    {
        $this->log('updated', $retailer);
    }

    public function deleted(Retailer $retailer)
    {
        $this->log('deleted', $retailer);
    }

    private function log($action, Retailer $retailer)
    {
        $description = $this->prepareDescription($action, $retailer);

        RetailerLog::create([
            'user_id' => Auth::id(), // Log the action user
            'retailer_id' => $retailer->id,
            'retailer_dba' => $retailer->dba, // Store the static retailer DBA
            'action' => $action,
            'description' => json_encode($description),
        ]);
    }

    private function prepareDescription($action, Retailer $retailer)
    {
        switch ($action) {
            case 'created':
                return $retailer->toArray();
    
            case 'updated':
                return [
                    'old' => $retailer->getOriginal(),
                    'new' => $retailer->getAttributes(),
                ];
    
            case 'deleted':
                // Only include specific fields in the deletion log
                return [
                    'first_name' => $retailer->first_name,
                    'last_name' => $retailer->last_name,
                    'email' => $retailer->email,
                    'phone' => $retailer->phone,
                 
                ];
    
            default:
                return [];
        }
    }
    
}
