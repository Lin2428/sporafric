<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntrventionDelivery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'intervention_id',
        'piece_id',
        'user_id',
    ];
}
