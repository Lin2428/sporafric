<?php

namespace App\Models;
use App\Enum\GeneratorStatus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devis extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'odoo_id',
        'customer_name',
        'customer_id',
        'number',
        'start_date',
        'end_date',
        'is_active',
        'is_retired',
        'forfait',
        'state',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

     protected static function booted()
    {
        
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function generators()
    {
        return $this->belongsToMany(Generator::class, 'devis_generators')
            ->withPivot([
                'status',
                'user_id', 
                'site',
                'code_site',
                'contact_name',
                'contact_phone',
                'contact_email',
                ])
                ->withTimestamps();
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}
