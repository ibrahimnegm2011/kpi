<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Menu;
use App\Enums\UserType;
use App\Models\Traits\HasAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * @property string $account_id
 * @property-read Collection<UserPermission> $permissions
 * @property-read Company $company
 * @property-read Department $department
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUlids, Notifiable, HasAccount;

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
            'is_active' => 'boolean',
            'is_agent' => 'boolean',
            'onboarded_at' => 'datetime',
            'type' => UserType::class,
        ];
    }

    public function account(): belongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function agent_accounts():BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'agent_accounts', 'user_id', 'account_id')
            ->withPivot('company_id', 'department_id', 'position');
    }

    public function agent_assignments(): HasMany
    {
        return $this->hasMany(AgentAssignment::class);
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

    public function hasPermission(...$permissions): bool
    {
        foreach ($permissions as $permission) {

            if(Auth::user()->type == UserType::AGENT && in_array(Menu::tryFrom($permission), Menu::agentItems())) {
                return true;
            }
        }

        return $this->loadMissing('permissions')
            ->permissions->whereIn('permission', $permissions)
            ->isNotEmpty();
    }

    public function addPermissions($permissions)
    {
        $this->permissions()->delete();

        $this->permissions()->createMany(Arr::map($permissions, fn ($value) => ['permission' => $value]));
    }
}
