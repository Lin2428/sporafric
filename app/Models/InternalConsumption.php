<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Consommation interne Odoo rattachée à un GE : comptée comme maintenance
 * (jour « M » du planning + coût) dans le rapport de location.
 */
class InternalConsumption extends Model
{
    protected $fillable = [
        'odoo_order_id',
        'generator_id',
        'order_number',
        'order_state',
        'order_date',
        'amount_untaxed',
        'serial',
        'hours',
        'description',
    ];

    protected $casts = [
        'order_date'     => 'datetime',
        'amount_untaxed' => 'float',
    ];

    public function generator(): BelongsTo
    {
        return $this->belongsTo(Generator::class);
    }

    public function scopeConfirmed($query)
    {
        return $query->whereIn('order_state', RentalPeriod::CONFIRMED_STATES);
    }
}
