<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specialization extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'specialization'
    ];

    /**
     * The doctors that belong to the specialization.
     */
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctors_specializations', 'specialization_id', 'doctor_id');
    }
}
