<?php

namespace App\Models;

use App\Enums\MeasureUnit;
use App\Models\Traits\HasAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * @method Builder forCurrentAgentAssignments()
 */
class Forecast extends Model
{
    use HasUlids, HasFactory, HasAccount;

    protected $guarded = [];

    public function casts()
    {
        return [
            'is_submitted' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function scopeSubmitted(Builder $query, string $value)
    {
        return match (intval($value)) {
            1 => $query->where('is_submitted', true),
            0 => $query->where('is_submitted', false),
            default => $query,
        };
    }

    public function scopeForCurrentAgentAssignments($query)
    {
        $userId = Auth::id();
        $accountId = session('selected_account');

        $pairs = AgentAssignment::query()
            ->where('user_id', $userId)
            ->where('account_id', $accountId)
            ->get(['department_id', 'company_id']);

        if ($pairs->isEmpty()) {
            return $query->whereRaw('0 = 1');
        }

        return $query->where(function ($q) use ($pairs) {
            $pairs->each(function ($pair) use ($q) {
                $q->orWhere(function ($q2) use ($pair) {
                    $q2->where('company_id', $pair['company_id'])
                        ->where('department_id', $pair['department_id']);
                });
            });
        });
    }

}
