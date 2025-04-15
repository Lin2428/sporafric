<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quartier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'district_id',
        'user_id',
    ];

    public function district():BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
