<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterventionPieces extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'intrvention_id',
        'piece_id',
        'generator_id',
        'qty',
        'price',
    ];

    public function intervention()
    {
        return $this->belongsTo(Intervention::class, 'intrvention_id');
    }

    public function piece()
    {
        return $this->belongsTo(Piece::class);
    }
}