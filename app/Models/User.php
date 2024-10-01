<?php
namespace App\Models;

use App\Models\RetailerAddress;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'userable_id',
        'userable_type',
        'created_by', 
    ];
    public function hasRole($role)
    {
        // Check based on the 'original_name' field instead of 'name'
        return $this->roles()->where('original_name', $role)->exists();
    }

    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the retailer associated with the user.
     */
    public function retailer()
    {
        return $this->hasOne(Retailer::class);
    }
  // In User.php model
public function lp()
{
    return $this->belongsTo(Lp::class, 'lp_id'); // Make sure 'lp_id' is the correct foreign key
}

    
}
