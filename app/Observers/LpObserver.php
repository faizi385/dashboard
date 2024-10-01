<?php
namespace App\Observers;

use App\Models\Lp;
use App\Models\LpLog;
use Illuminate\Support\Facades\Auth;

class LpObserver
{
    public function created(Lp $lp)
    {
        $this->log('created', $lp);
    }

    public function updated(Lp $lp)
    {
        $this->log('updated', $lp);
    }

    public function deleted(Lp $lp)
{
    // Only log the deletion; the actual deletion is handled by soft deletes.
    $this->log('deleted', $lp);
}

    

    private function log($action, Lp $lp)
    {
        $description = $this->prepareDescription($action, $lp);

        LpLog::create([
            'user_id' => Auth::id(), // Log the action user
            'lp_id' => ($action === 'deleted') ? $lp->id : $lp->id, // Set lp_id as necessary
            'action' => $action,
            'description' => json_encode($description),
            'dba' => $lp->dba, // Store the static LP DBA
        ]);
    }

    private function prepareDescription($action, Lp $lp)
    {
        switch ($action) {
            case 'created':
                return $lp->toArray();

            case 'updated':
                return [
                    'old' => $lp->getOriginal(),
                    'new' => $lp->getAttributes(),
                ];

            case 'deleted':
                // Include specific fields for the deletion log
                return [
                    'id' => $lp->id,
                    'dba' => $lp->dba,
                    'name' => $lp->name,
                    // Add any other necessary fields here
                ];

            default:
                return [];
        }
    }
}
