<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevisGenerator extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'devis_id',
        'generator_id',
        'status',
        'user_id',
    ];

    public function contract()
    {
        return $this->belongsTo(Devis::class);
    }

    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }
}
