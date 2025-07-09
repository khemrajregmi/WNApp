<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Doctor extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'years_of_experience'
    ];

    /**
     * The specializations that belong to the doctor.
     */
    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'doctors_specializations', 'doctor_id', 'specialization_id');
    }
}
