<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacilityCategory extends Model
{
    protected $fillable = [
        'facility_name',
        'icon_marker',
    ];

    public function reportDetails(): HasMany
    {
        return $this->hasMany(ReportDetail::class, 'category_id');
    }
}
