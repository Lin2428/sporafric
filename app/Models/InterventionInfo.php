<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterventionInfo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'intervention_id',
        'devis_numero',
        'devis_date',
        'devis_montant',
        'bc_numero',
        'bc_date',
        'bc_fiche',
        'user_id',
    ];
}
