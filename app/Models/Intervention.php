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
        'cancelled',
        'raison',
        'user_id',
    ];

    protected $with = ['interventionTechniciens', 'pieces', 'generator','newGenerator'];


    protected static function booted()
    {
        static::updated(function ($intervention) {
            if($intervention->type == InterventionType::RETRAIT->value && $intervention->status == InterventionStatus::TERMINEE->value){
                Generator::where('id', $intervention->generator_id)
                ->update(['status' => GeneratorStatus::EN_REVU->value]);
            }
        });

        static::updated(function ($intervention) {
            if($intervention->type == InterventionType::RETRAIT->value && $intervention->status == InterventionStatus::TERMINEE->value){
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

    public function toCalendarEvent(): CalendarEvent|array
    {
        return CalendarEvent::make($this)
            ->title(InterventionType::from($this->type)->label())
            ->start(Carbon::make($this->start_date != null ? $this->start_date : $this->date_planifiee))
            ->end(Carbon::make($this->end_date != null ? $this->end_date : $this->date_planifiee))
            ->backgroundColor(
                match ($this->status) {
                (int) InterventionStatus::PLANIFIEE->value => '#3b82f6', 
                (int) InterventionStatus::EN_COURS->value => '#f59e0b', // amber-500
                (int) InterventionStatus::TERMINEE->value => '#36d16cff', // gray-500 
                default => '#3b82f6', // default to blue-500
                }
            )
            ->extendedProps([
                'customer' => $this->customer?->name ?? $this->contract?->customer?->name ?? $this->devis?->customer?->name,
                'type_service' => $this->type_service,
            ])
            ->key($this->id)
            ->allDay(true);
    }
}
