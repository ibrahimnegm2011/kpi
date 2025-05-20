<?php

namespace App\Models;

use App\Enums\MeasureUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kpi extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = [];

    public function casts()
    {
        return [
            'measure_unit' => MeasureUnit::class,
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function forecasts(): HasMany
    {
        return $this->hasMany(Forecast::class);
    }

    public function scopeActive(Builder $query, string $value)
    {
        match (intval($value)) {
            1 => $query->where('is_active', true),
            0 => $query->where('is_active', false),
            default => '',
        };

        return $query;
    }
}
