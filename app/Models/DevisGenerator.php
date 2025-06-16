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
        'site',
        'code_site',
        'contact_name',
        'contact_phone',
        'contact_email',
    ];

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }
}
