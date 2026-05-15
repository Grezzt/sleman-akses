<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'photo_url',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ReportDetail::class, 'report_id');
    }

    public function validation(): HasOne
    {
        return $this->hasOne(Validation::class, 'report_id');
    }
}
