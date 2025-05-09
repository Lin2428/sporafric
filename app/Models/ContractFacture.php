<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractFacture extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_id',
        'intervention_id',
        'montant',
        'note',
        'is_paid',
        'start_date',
        'end_date',
        'user_id',
    ];
    
    protected $casts = [
        'montant' => 'integer',
        'is_paid' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $with = [
        'intervention',
    ];

    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }
}
