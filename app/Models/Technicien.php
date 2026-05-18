<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Technicien extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'odoo_id',
        'name',
        'phone',
        'email',
        'photo',
        'job',
        'user_id',
        'is_active',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
