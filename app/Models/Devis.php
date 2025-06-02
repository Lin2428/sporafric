<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devis extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'customer_id',
        'generator_id',
        'number',
        'site',
        'code_site',
        'start_date',
        'end_date',
        'is_active',
        'is_retired',
        'forfait',
        'user_id',
        'adress',
        'contact_name',
        'contact_phone',
        'contact_email',
        'lat',
        'lng',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }
    public function devisGenerator()
    {
        return $this->hasOne(DevisGenerator::class)->where('status', true);
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}
