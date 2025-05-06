<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Piece extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'designation',
        'image',
        'duree_vie',
        'pr',
        'pv',
        'user_id',
    ];

    public function interventions()
    {
        return $this->belongsToMany(Intervention::class, 'intrvention_deliveries');
    }
}
