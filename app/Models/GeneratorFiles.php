<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneratorFiles extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'generator_id',
        'file_name',
        'file_rename',
        'user_id',
    ];
}
