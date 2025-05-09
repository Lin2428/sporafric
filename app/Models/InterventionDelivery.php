<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterventionDelivery extends Model
{
    use SoftDeletes;

    protected $table = 'intrvention_deliveries';

    protected $fillable = [
        'intervention_id',
        'piece_id',
        'user_id',
    ];
}
