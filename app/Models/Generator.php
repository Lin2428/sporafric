<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Generator extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'odoo_id',
        'name',
        'image',
        'reference',
        'power',
        'voltage',
        'frequency',
        'serial_number',
        'start-up',
        'status',
        'houres',
        'next_vidange',
        'fuel_type',
        'type',
        'adresse',
        'lat',
        'lng',
        'user_id',
        'prochain_visite',
        'vidange',
    ];

    protected $casts = [
        'start-up' => 'datetime',
        'vidange' => 'boolean',
    ];


    protected $with = [
        'contractGenerator',
        'etat',
        'files',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contractGenerator()
    {
        return $this->hasOne(ContractGenerator::class);
    }

    public function devisGenerator()
    {
        return $this->hasOne(DevisGenerator::class)
        ->where('status', true)
        ->where('is_retired',false);
    }

    public function pieces()
    {
        return $this->hasMany(InterventionPieces::class);
    }

    public function etat()
    {
        return $this->hasOne(Checklist::class);
    }

    public function files()
    {
        return $this->hasMany(GeneratorFiles::class);
    }
}
