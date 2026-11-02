<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Période d'utilisation d'un GE de location (ligne de location Odoo).
 * Dates réelles (picked_up_at / returned_at) prioritaires sur les dates prévues.
 */
class RentalPeriod extends Model
{
    /** Commandes prises en compte (devis confirmés ou verrouillés). */
    public const CONFIRMED_STATES = ['sale', 'done'];

    protected $fillable = [
        'odoo_line_id',
        'odoo_order_id',
        'generator_id',
        'devis_id',
        'customer_id',
        'order_number',
        'order_state',
        'rental_status',
        'planned_start',
        'planned_return',
        'picked_up_at',
        'returned_at',
        'amount_untaxed',
        'invoice_status',
        'invoice_numbers',
    ];

    protected $casts = [
        'planned_start'  => 'datetime',
        'planned_return' => 'datetime',
        'picked_up_at'   => 'datetime',
        'returned_at'    => 'datetime',
        'amount_untaxed' => 'float',
    ];

    public function generator(): BelongsTo
    {
        return $this->belongsTo(Generator::class);
    }

    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeConfirmed($query)
    {
        return $query->whereIn('order_state', self::CONFIRMED_STATES);
    }

    /** Début effectif : départ réel, sinon début prévu. */
    public function effectiveStart(): ?Carbon
    {
        return $this->picked_up_at ?? $this->planned_start;
    }

    /**
     * Fin effective : retour réel ; GE parti mais pas revenu → aujourd'hui (en cours) ;
     * sinon retour prévu.
     */
    public function effectiveEnd(): ?Carbon
    {
        if ($this->returned_at) {
            return $this->returned_at;
        }
        if ($this->isOngoing()) {
            return now()->max($this->effectiveStart() ?? now());
        }

        return $this->planned_return;
    }

    /**
     * GE actuellement chez le client : parti (mouvement de départ, ou statut Odoo « return » =
     * retour attendu — certains départs sont validés sans mouvement de stock) et pas revenu.
     */
    public function isOngoing(): bool
    {
        return $this->returned_at === null
            && ($this->picked_up_at !== null || $this->rental_status === 'return');
    }
}
