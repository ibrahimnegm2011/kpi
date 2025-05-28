<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property-read User $user
 * @property-read Account $account
 */
class AgentAssignment extends Pivot
{
    public $table = 'agent_assignments';

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function account(): belongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function company(): belongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function department(): belongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
