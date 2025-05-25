<?php

namespace App\Models;

use App\Models\Traits\HasAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @method Builder account(string $value)
 */
class Company extends Model
{
    use HasFactory, HasUlids, HasAccount;

    protected $guarded = [];
}
