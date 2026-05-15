<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Validation extends Model
{
    protected $fillable = [
        'user_id',
        'report_id',
        'approval_status',
        'evaluation_note',
        'validation_date',
    ];

    protected $casts = [
        'validation_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function mapLocation(): HasOne
    {
        return $this->hasOne(MapLocation::class, 'validation_id');
    }
}
