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
        'status',
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
