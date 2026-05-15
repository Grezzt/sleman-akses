<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapLocation extends Model
{
    protected $fillable = [
        'validation_id',
        'latitude',
        'longitude',
        'publish_date',
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function validation(): BelongsTo
    {
        return $this->belongsTo(Validation::class);
    }
}
