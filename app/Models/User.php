<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Menu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read Collection<UserPermission> $permissions
 * @property-read Company $company
 * @property-read Department $department
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUlids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'is_representative' => 'boolean',
            'registered_at' => 'datetime',
        ];
    }

    public function company(): belongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): belongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(UserPermission::class);
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

    public function scopeType(Builder $query, string $type)
    {
        if ($type == 'admin') {
            return $query->where('is_admin', true)
                ->where('is_representative', false);
        }
        if ($type == 'representative') {
            return $query->where('is_representative', true)
                ->where('is_admin', false);
        }

        if ($type == 'normal') {
            return $query->where('is_representative', false)
                ->where('is_admin', false);
        }

        return $query;
    }

    public function hasPermission(...$permissions): bool
    {
        foreach ($permissions as $permission) {
            if(Auth::user()->is_representative && in_array(Menu::tryFrom($permission), Menu::representativesItems())) {
                return true;
            }
        }

        return $this->is_admin || $this->hasNonAdminPermission(...$permissions);
    }

    public function hasNonAdminPermission(...$permissions): bool
    {
        $permissions = Arr::wrap($permissions);

        return $this->loadMissing('permissions')
            ->permissions->whereIn('permission', $permissions)
            ->isNotEmpty();
    }

    public function hasAnyPermission()
    {
        $this->loadMissing('permissions');

        return $this->is_admin || $this->permissions->isNotEmpty();
    }

    public function addPermissions($permissions)
    {
        $this->permissions()->delete();

        $this->permissions()->createMany(Arr::map($permissions, fn ($value) => ['permission' => $value]));
    }
}
