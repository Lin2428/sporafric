<?php

namespace App\Models;

use App\Enum\GeneratorStatus;
use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use Carbon\Carbon;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Intervention extends Model implements Eventable
{
    use SoftDeletes, HasFactory;


    protected $fillable = [
        'numero',
        'type_service',
        'type_activite',
        'contract_id',
        'generator_id',
        'houres',
        'devis_id',
        'customer_id',
        'generator_name',
        'generator_reference',
        'power',
        'serial_number',
        'date_prise_appel',
        'date_planifiee',
        'type',
        'identifiant',
        'description_panne',
        'start_date',
        'end_date',
        'compteur',
        'fiche',
        'facturable',
        'astrinte',
        'status',
        'montant',
        'new_generator_id',
        'numero_devis',
        'cancelled',
        'raison',
        'travaux',
        'site',
        'is_synced',
        'signature_path',
        'signed_by',
        'signed_at',
        'signature_refusal',
        'client_email',
        'report_sent_at',
        'report_error',
        'user_id',
    ];

    protected $casts = [
        'signed_at'      => 'datetime',
        'report_sent_at' => 'datetime',
    ];

    protected $with =['interventionTechniciens', 'pieces', 'generator', 'newGenerator', 'fiches'];


    protected static function booted()
    {
        static::updated(function ($intervention) {
            if ($intervention->type == InterventionType::RETRAIT->value && $intervention->status == InterventionStatus::TERMINEE->value) {
                Generator::where('id', $intervention->generator_id)
                    ->update(['status' => GeneratorStatus::EN_REVU->value]);
            }
        });

        static::updated(function ($intervention) {
            if ($intervention->type == InterventionType::RETRAIT->value && $intervention->status == InterventionStatus::TERMINEE->value) {
                Generator::where('id', $intervention->generator_id)
                    ->update(['status' => GeneratorStatus::EN_REVU->value]);
            }
        });
    }
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }

    public function newGenerator()
    {
        return $this->belongsTo(Generator::class, 'new_generator_id');
    }

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function interventionTechniciens(): BelongsToMany
    {
        return $this->belongsToMany(Technicien::class, 'intervention_techniciens');
    }

    public function pieces()
    {
        return $this->belongsToMany(Piece::class, 'intervention_pieces', 'intervention_id',)->withPivot(['qty', 'price']);
    }

    public function fiches()
    {
        return $this->hasMany(InterventionFiche::class);
    }

    /** Photos prises depuis l'app mobile (table `photos`). */
    public function photos()
    {
        return $this->hasMany(Photo::class, 'intervention_id');
    }

    public function toCalendarEvent(): CalendarEvent|array
    {
        $type   = InterventionType::tryFrom((string) $this->type);
        $status = InterventionStatus::tryFrom((string) $this->status) ?? InterventionStatus::PLANIFIEE;
        $start  = $this->start_date ?? $this->date_planifiee ?? $this->created_at;

        // Carte claire + liseré couleur du type (voir components/calendar-event + canlendar-page)
        return CalendarEvent::make($this)
            ->title($type?->label() ?? 'Intervention')
            ->start($start)
            ->end($this->end_date ?? $this->date_planifiee ?? $start)
            ->backgroundColor('transparent')
            ->textColor('#1f2937')
            ->classNames(['gmao-ev', 'gmao-st-' . $status->value])
            ->extendedProps([
                'customer'     => $this->customer?->name ?? $this->contract?->customer?->name ?? $this->devis?->customer?->name,
                'type_service' => $this->type_service,
                'color'        => $type?->color() ?? '#94a3b8',
                'status'       => $status->value,
                'status_label' => ucfirst($status->label()),
                'detail'       => $this->site ?: ($this->generator?->name ?? $this->generator_name),
                'time'         => $this->start_date ? \Illuminate\Support\Carbon::parse($this->start_date)->format('H:i') : null,
                'numero'       => $this->numero,
            ])
            ->key($this->id)
            ->allDay(true);
    }
}
