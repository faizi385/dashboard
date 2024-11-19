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
    
        // Get the roles, if available, or an empty array
        $roles = $user->roles->pluck('name')->toArray() ?? [];
    
        Log::create([
            'action' => 'created',
            'user_id' => $user->id,
            'action_user_id' => $actionUserId, // Store the action user's ID, or null if not authenticated
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'Name' => $user->first_name . ' ' . $user->last_name,
                'Email' => $user->email,
                'Phone' => $user->phone, // Include the phone number here
               
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
        $actionUser = auth()->user(); // Get the currently authenticated user
        
        $original = $user->getOriginal();
        $changes = $user->getChanges();
    
        // Prepare a new changes array
        $loggedChanges = [];
    
        // Loop through the changes and exclude email field
        foreach ($changes as $key => $value) {
            if ($key === 'password') {
                $loggedChanges[$key] = '**********'; // Mask password changes
            } elseif ($key !== 'email') {
                $loggedChanges[$key] = $value; // Log other changes as is, excluding email
            }
        }
    
        Log::create([
            'action' => 'updated',
            'user_id' => $user->id,
            'action_user_id' => $actionUser ? $actionUser->id : null,
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'old' => array_diff_key($original, ['email' => '']), // Exclude old email from log
                'new' => $loggedChanges,
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
            'action_user_id' => $actionUser->id, // Store the action user's ID
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'name' => $user->first_name . ' ' . $user->last_name,
                // Do not include email here
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
            'action_user_id' => $actionUser->id, // Store the action user's ID
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
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
            'action_user_id' => $actionUser->id, // Store the action user's ID
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
            ]),
        ]);
    }
}
