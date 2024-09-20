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
        $this->log('deleted', $lp);
    }

    private function log($action, Lp $lp)
    {
        // Check if the $lp object has 'dba' value
        if (empty($lp->dba)) {
            throw new \Exception("The DBA value is missing.");
        }
    
        // Prepare description based on action (created, updated, deleted)
        $description = $this->prepareDescription($action, $lp);
    
        // Log the action
        LpLog::create([
            'user_id' => Auth::id(), // Log authenticated user
            'lp_id' => $lp->id,
            'action' => $action,
            'description' => json_encode($description),
            'dba' => $lp->dba, // Ensure 'dba' is included here
        ]);
    }
    

    private function prepareDescription($action, Lp $lp)
    {
        switch ($action) {
            case 'created':
                return $lp->toArray(); // Log all attributes when created

            case 'updated':
                // Log changes with old and new values
                return [
                    'old' => $lp->getOriginal(),
                    'new' => $lp->getAttributes(),
                ];

            case 'deleted':
                return $lp->toArray(); // Log all attributes when deleted

            default:
                return [];
        }
    }
}
