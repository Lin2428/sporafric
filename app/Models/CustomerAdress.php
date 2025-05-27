<?php

namespace App\Models;

use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\Quartier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAdress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'address',
        'user_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }

    // public function country()
    // {
    //     return $this->belongsTo(Country::class);
    // }
    // public function city()
    // {
    //     return $this->belongsTo(City::class);
    // }
    // public function district()
    // {
    //     return $this->belongsTo(District::class);
    // }
    // public function quartier()
    // {
    //     return $this->belongsTo(Quartier::class);
    // }
}
