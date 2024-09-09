<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'user_id',
        'action_user_id', // Add this field
        'ip_address',
        'description',
        'created_at',
        'updated_at',
    ];
// In Log.php model

public function actionUser()
{
    return $this->belongsTo(User::class, 'action_user_id');
}

public function user()
{
    return $this->belongsTo(User::class);
}

}
