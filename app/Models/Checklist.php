<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $fillable = [
        'type',
        'is_clean',
        'is_functional',
        'electrical_value',
        'is_maintained',
        'mechanical_value',
        'hour_number',
        'next_vidange',
        'user_id',
    ];
}
