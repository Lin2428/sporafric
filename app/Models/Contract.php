<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
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
        'forfait',
        'user_id',
        'customer_adresse_id',
        'contact_name',
        'contact_phone',
        'contact_email',
        'lat',
        'lng',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_fixed' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function ($model) {
            ContractGenerator::create([
                'contract_id' => $model->id,
                'generator_id' => $model->generator_id,
                'status' => true,
                'user_id' => auth()->user()->id,
            ]);
        });

        static::updating(function ($model) {
            dd($model);
        });
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }
    public function contractGenerator()
    {
        return $this->hasOne(ContractGenerator::class)->where('status', true);
    }

    public function customerAdress()
    {
        return $this->belongsTo(CustomerAdress::class, 'customer_adresse_id', 'id');
    }
}
