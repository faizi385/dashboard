<?php

namespace App\Observers;
use App\Models\Log;
use App\Models\User;
use Illuminate\Support\Facades\Request;
class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $actionUser = auth()->user(); // Get the currently authenticated user
        
        // Handle the case where there is no authenticated user
        $actionUserId = $actionUser ? $actionUser->id : null;

        Log::create([
            'action' => 'created',
            'user_id' => $user->id,
            'action_user_id' => $actionUserId, // Store the action user's ID, or null if not authenticated
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->toArray(),
                'updated_at' => $user->updated_at,
            ]),
        ]);
    }
    
    public function updated(User $user)
    {
        $actionUser = auth()->user(); // Get the currently authenticated user
    
        $original = $user->getOriginal();
    
        Log::create([
            'action' => 'updated',
            'user_id' => $user->id,
            'action_user_id' => $actionUser->id, // Store the action user's ID
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'old' => $original,
                'new' => $user->getChanges(),
            ]),
        ]);
    }
    


    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
