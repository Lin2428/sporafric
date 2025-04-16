<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Generator extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'image',
        'modele',
        'power',
        'voltage',
        'frequency',
        'serial_number',
        'start-up',
        'status',
        'houres',
        'next_vidange',
        'fuel_type',
        'user_id',
    ];
    
    protected $casts = [
        'start-up' => 'datetime',
        'next_vidange' => 'datetime',
    ];


    protected $with = [
        'contractGenerator',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contractGenerator()
    {
        return $this->hasOne(ContractGenerator::class)->where('status', true);
    }

}
