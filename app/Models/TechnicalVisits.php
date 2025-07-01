<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnicalVisits extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'type_service',
        'contract_id',
        'devis_id',
        'generator_id',
        'control_1',
        'control_2',
        'control_3',
        'control_4',
        'control_5',
        'control_6',
        'control_7',
        'control_8',
        'control_9',
        'control_10',
        'control_11',
        'control_12',
        'control_13',
        'control_14',
        'control_15',
        'control_16',
        'control_battery',
        'control_circuit',
        'control_tension',
        'control_tension_2',
        'control_intensite',
        'control_frequence',
        'date',
        'user_id',
    ];

    protected $casts = [
    'control_tension' => 'array',
    'control_tension_2' => 'array',
    'control_intensite' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
