<?php
// App/Observers/RetailerLogObserver.php

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
            'user_id' => Auth::id(),
            'retailer_id' => $retailer->id,
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
                // Get the original and updated attributes if needed
                return [
                    'old' => $retailer->getOriginal(),
                    'new' => $retailer->getAttributes(),
                ];

            case 'deleted':
                return $retailer->toArray();

            default:
                return [];
        }
    }
}
