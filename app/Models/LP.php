<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lp extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'dba',
        'primary_contact_email',
        'primary_contact_phone',
        'primary_contact_position',
        'password',
        'user_id',
        'status',
        // Add a field for modified email if needed
        'modified_email', // Optional: if you want to keep track of modified emails
    ];

    public function address()
    {
        return $this->hasMany(LpAddress::class);
    }

    public function logs()
    {
        return $this->hasMany(LpLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function carveouts()
    {
        return $this->hasMany(Carveout::class);
    }
    public function reports()
    {
        return $this->hasMany(Report::class, 'lp_id'); // assuming 'lp_id' is the foreign key in the reports table
    }
    // Override the delete method to modify email
    public function delete()
    {
        // Modify email for soft deletion
        if ($this->primary_contact_email) {
            $this->primary_contact_email = str_replace('@', '_deleted@', $this->primary_contact_email); // Modify email
            $this->save(); // Save the changes to the database
        }

        return parent::delete(); // Call the parent delete method to perform soft delete
    }
}
