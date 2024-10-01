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
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone, // Include the phone number here
                'role' => $roles, // Ensure roles are logged correctly
                'updated_at' => $user->updated_at,
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

        // Get the roles if they've been updated, or fallback to current roles
        $roles = $user->roles->pluck('name')->toArray() ?? [];

        Log::create([
            'action' => 'updated',
            'user_id' => $user->id,
            // 'action_user_id' => $actionUser->id, // Store the action user's ID
            'ip_address' => request()->ip(),
            'description' => json_encode([
                'old' => $original,
                'new' => array_merge($changes, ['roles' => $roles]), // Add roles to the changes array
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
        
        Log::create([
            'action' => 'deleted',
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
