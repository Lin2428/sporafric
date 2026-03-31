<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractFiles extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_id',
        'file_name',
        'file_rename',
        'user_id',
    ];
}
