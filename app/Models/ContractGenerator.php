<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractGenerator extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'contract_id',
        'generator_id',
        'site',
        'code_site',
        'contact_name',
        'contact_phone',
        'contact_email',
        'status',
        'forfait',
        'user_id',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }
}
