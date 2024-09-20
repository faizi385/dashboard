<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lp extends Model
{
   
   
    protected $fillable = ['name', 'dba', 'primary_contact_email', 'primary_contact_phone', 'primary_contact_position','password','user_id'];


   // In Lp model (Lp.php)
public function address()
{
    return $this->hasMany(LpAddress::class);
}

// app/Models/Lp.php

public function logs()
{
    return $this->hasMany(LpLog::class);
}
// app/Models/Lp.php

public function user()
    {
        return $this->belongsTo(User::class);
    }

}
