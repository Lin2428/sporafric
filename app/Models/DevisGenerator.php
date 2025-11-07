<?php

namespace App\Models;

use App\Enum\DevisStats;
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
        'forfait',
        'old_generator_id',
        'is_retired',
    ];

    protected $with = ['devis', 'generator', 'oldGenerator'];

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }

    public function oldGenerator()
    {
        return $this->belongsTo(Generator::class, 'old_generator_id');
    }
}
