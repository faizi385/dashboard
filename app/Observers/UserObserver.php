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
                'Name' => $user->first_name . ' ' . $user->last_name,
                'Email' => '*****', // Mask the email on creation
                'Phone' => $user->phone,
                'Created At' => $user->created_at->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        // Skip logging if the user is being deleted
        if ($user->isDirty('deleted_at') && $user->deleted_at !== null) {
            return;
        }
    
        $actionUser = auth()->user(); // Get the currently authenticated user
        
        $original = $user->getOriginal(); // Original data before update
        $changes = $user->getChanges(); // New changes after update
        
        // Prepare a new changes array
        $loggedChanges = [];
        
        // Loop through the changes and mask sensitive fields
        foreach ($changes as $key => $value) {
            if ($key === 'password') {
                $loggedChanges[$key] = '**********'; // Mask password changes
            } elseif ($key === 'email') {
                $loggedChanges[$key] = '*****'; // Mask email changes in new data
            } else {
                $loggedChanges[$key] = $value; // Log other changes as is
            }
        }
    
        // Mask the old email as '*****' if it exists
        $original['email'] = '*****';
        
        Log::create([
            'action' => 'updated',
            'user_id' => $user->id,
            'action_user_id' => $actionUser ? $actionUser->id : null,
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'old' => $original, // Include masked email in the 'old' data
                'new' => $loggedChanges, // Include masked email in the 'new' data
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
        $actionUser = auth()->user();
    
        // Exclude email from the description when the user is deleted
        Log::create([
            'action' => 'deleted',
            'user_id' => $user->id,
            'action_user_id' => $actionUser ? $actionUser->id : null, // Store the action user's ID
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'name' => $user->first_name . ' ' . $user->last_name,
                'Email' => '*****', // Mask email during deletion
                'Phone' => $user->phone, 
            ]),
        ]);
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        $actionUser = auth()->user();
        
        Log::create([
            'action' => 'restored',
            'user_id' => $user->id,
            'action_user_id' => $actionUser ? $actionUser->id : null, // Store the action user's ID
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => '*****', // Mask email during restoration
            ]),
        ]);
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        $actionUser = auth()->user();
        
        Log::create([
            'action' => 'force deleted',
            'user_id' => $user->id,
            'action_user_id' => $actionUser ? $actionUser->id : null, // Store the action user's ID
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => '*****', // Mask email during force deletion
            ]),
        ]);
    }
}
