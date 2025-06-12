<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * @method static Builder byAccount(string $value)
 */
trait HasAccount
{
    public function scopeByAccount(Builder $query, string $value)
    {
        return $query->where('account_id', $value);
    }

    public static function forAccount($accountId = null): Collection
    {
        $accountId = $accountId ?? Auth::user()->account_id;

        return self::byAccount($accountId)->get();
    }
}
