<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    protected $fillable = [
        'intervention_id',
        'technical_visit_id',
        'filename',
        'path',
    ];

    public function intervention(): BelongsTo
    {
        return $this->belongsTo(Intervention::class);
    }

    public function technicalVisit(): BelongsTo
    {
        return $this->belongsTo(TechnicalVisits::class);
    }

    public function url(): string
    {
        return rtrim(config('app.url'), '/') . '/storage/' . ltrim($this->path, '/');
    }
}
