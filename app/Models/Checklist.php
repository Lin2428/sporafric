<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $fillable = [
        'generator_id',
        'devis_id',
        'technicien_id',
        'is_clean',
        'is_functional',
        'electrical_value',
        'is_maintained',
        'mechanical_value',
        'hour_number',
        'next_vidange',
        'technicien_id_after',
        'is_clean_after',
        'is_functional_after',
        'electrical_value_after',
        'is_maintained_after',
        'mechanical_value_after',
        'hour_number_after',
        'next_vidange_after',
        'user_id',
    ];
}
