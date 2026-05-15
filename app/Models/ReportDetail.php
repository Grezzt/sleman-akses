<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportDetail extends Model
{
    protected $fillable = [
        'report_id',
        'category_id',
        'availability_status',
    ];

    protected $casts = [
        'availability_status' => 'boolean',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FacilityCategory::class, 'category_id');
    }
}
