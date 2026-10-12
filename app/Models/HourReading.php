<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Relevé d'heures de fonctionnement (HF) d'un groupe électrogène.
 * `houres` null = ligne de tournée pas encore relevée.
 */
class HourReading extends Model
{
    protected $fillable = [
        'hour_round_id',
        'generator_id',
        'customer_id',
        'site',
        'code_site',
        'position',
        'houres',
        'previous_houres',
        'prochain_visite',
        'next_vidange',
        'read_at',
        'note',
        'user_id',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function round(): BelongsTo
    {
        return $this->belongsTo(HourRound::class, 'hour_round_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(Generator::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
