<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Intervention extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_id',
        'date_prise_appel',
        'date_planifiee',
        'type',
        'identifiant',
        'description_panne',
        'start_date',
        'end_date',
        'compteur',
        'fiche',
        'facturable',
        'astrinte',
        'status',
        'cancelled',
        'raison',
        'user_id',
    ];
}
