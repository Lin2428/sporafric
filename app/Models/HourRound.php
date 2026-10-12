<?php

namespace App\Models;

use App\Enum\HourRoundStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Tournée de relevés d'heures de fonctionnement assignée à un technicien.
 */
class HourRound extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'technicien_id',
        'date',
        'status',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function technicien(): BelongsTo
    {
        return $this->belongsTo(Technicien::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(HourReading::class)->orderBy('position')->orderBy('id');
    }

    /**
     * Recalcule le statut à partir des lignes renseignées.
     */
    public function refreshStatus(): void
    {
        $total = $this->readings()->count();
        $done  = $this->readings()->whereNotNull('houres')->count();

        $status = match (true) {
            $total > 0 && $done >= $total => HourRoundStatus::TERMINEE,
            $done > 0                     => HourRoundStatus::EN_COURS,
            default                       => HourRoundStatus::PLANIFIEE,
        };

        if ($this->status !== $status->value) {
            $this->update(['status' => $status->value]);
        }
    }
}
