<?php

namespace App\Models;

use App\Enums\ClosedOption;
use App\Enums\ReminderOption;
use App\Models\Traits\HasAccount;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * @method Builder forCurrentAgentAssignments()
 */
class Forecast extends Model
{
    use HasAccount, HasFactory, HasUlids;

    protected $guarded = [];

    public function casts()
    {
        return [
            'is_submitted' => 'boolean',
            'is_closed' => 'boolean',
            'submitted_at' => 'datetime',
            'auto_close_option' => ClosedOption::class,
            'reminder_option' => ReminderOption::class,
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

    public function scopeClosed(Builder $query, string $value)
    {
        return match (intval($value)) {
            1 => $query->where('is_closed', true),
            0 => $query->where('is_closed', false),
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

    public function isSubmittable()
    {
        if($this->is_closed) {
            return false;
        }

        $date = Carbon::create()->month((int) $this->month)->year((int) $this->year)->day(1);
        if($date->lessThan(now())) {
            return false;
        }
        return true;
    }
}
