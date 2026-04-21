<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorFilter extends Model
{
    protected $fillable = [
        'operator',
        'label',
    ];
}
