<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SynchronizeParameter extends Model
{
    protected $fillable = [
        'model',
        'field',
        'field_type',
        'operator_filter_id',
        'value',
        'is_active',
    ];
    protected $with = [
        'operatorFilter',
    ];
    protected $casts = [
        'value' => 'array',
    ];

    public function operatorFilter()
    {
        return $this->belongsTo(OperatorFilter::class, 'operator_filter_id');
    }
}
