<?php

namespace App\Models;

use App\Enums\Permission;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory, HasUlids;

    public $timestamps = false;

    protected $guarded = [];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id', 'id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function kpis(): HasMany
    {
        return $this->hasMany(Kpi::class);
    }

    public function forecasts(): HasMany
    {
        return $this->hasMany(Forecast::class);
    }
}
