<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class Role extends Model
{
    use HasFactory,HasRoles;

    public function hasRole($role)
    {
        // Check based on the 'original_name' field instead of 'name'
        return $this->roles()->where('original_name', $role)->exists();
    }
    
    use SoftDeletes;
}
