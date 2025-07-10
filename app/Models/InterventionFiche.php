<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterventionFiche extends Model
{
    protected $fillable = [
        'intervention_id',
        'fiche',
    ];

    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }
}
