<?php

namespace App\Models;

use App\Enum\DevisStats;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'note',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    public function contractGenerator()
    {
        return $this->hasOne(ContractGenerator::class,)
            ->whereHas('contract', function ($query) {
                $query->where('deleted_at', null);
            });
    }

    public function oldContractGenerator()
    {
        return $this->hasOne(ContractGenerator::class, 'old_generator_id')
            ->whereHas('contract', function ($query) {
                $query->whereNull('deleted_at');
            });
    }

    public function devisGenerator()
    {
        return $this->hasOne(DevisGenerator::class)
            ->where('is_retired', false)
            ->where('status', true)
            ->latest('created_at');
    }

    public function notRetiredDevis()
    {
        return $this->hasMany(DevisGenerator::class)
            ->where('is_retired', false)
            ->where('devis_id', '<>', $this->devisGenerator?->devis_id)
            ->where('status', true)
            ->get();
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

    public function getNextRecordLocation(): ?self
    {
        return self::where('id', '>', $this->id)
            ->whereNotNull('odoo_id')
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->first();
    }


    public function getPreviousRecordLocation(): ?self
    {
        return self::where('id', '<', $this->id)
            ->whereNotNull('odoo_id')
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->first();
    }

    public function getNextRecordMaintenance(): ?self
    {
        return self::where('id', '>', $this->id)
            ->whereNull('odoo_id')
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->first();
    }


    public function getPreviousRecordMaintenance(): ?self
    {
        return self::where('id', '<', $this->id)
            ->whereNull('odoo_id')
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->first();
    }
}
