<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'years_of_experience'
    ];
}
