<?php

namespace App\Models;

use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\Quartier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'odoo_id',
        'city',
        'contact_c_name',
        'contact_c_email',
        'contact_c_phone',
        'contact_l_name',
        'contact_l_email',
        'contact_l_phone',
        'logo',
        'user_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    // protected $with = ['customerAdresses'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
