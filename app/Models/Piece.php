<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Piece extends Model
{
    use SoftDeletes, HasFactory;

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
    return $this->belongsToMany(Intervention::class, 'intervention_pieces')
        ->withPivot(['qty', 'price']);
}
}
