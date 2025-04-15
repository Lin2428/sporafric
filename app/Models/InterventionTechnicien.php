<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterventionTechnicien extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'intervention_id',
        'technicien_id',
    ];
}
