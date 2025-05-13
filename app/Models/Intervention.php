<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Intervention extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type_location',
        'contract_id',
        'customer_id',
        'generator',
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
        'cancelled',
        'raison',
        'user_id',
    ];

    protected $with = ['interventionTechniciens', 'pieces'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
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
        return $this->belongsToMany(Piece::class, 'intervention_pieces', 'intrvention_id', 'piece_id')->withPivot(['qty', 'price']);
    }

    public function infos()
    {
        return $this->hasOne(InterventionInfo::class, 'intervention_id');
    }
}
