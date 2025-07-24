<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $fillable = [
        'generator_id',
        'devis_id',
        'technicien_id',
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
        'control_tension',
        'control_tension_2',
        'control_intensite',
        'control_frequence',
        'responsable',
        'user_id',
    ];

      protected $casts = [
    'control_tension' => 'array',
    'control_tension_2' => 'array',
    'control_intensite' => 'array',
    ];

    public function technicien() 
    {
        return $this->belongsTo(Technicien::class);
    } 

    public function devis() 
    {
        return $this->belongsTo(Devis::class);
    } 
}
