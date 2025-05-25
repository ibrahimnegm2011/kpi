<?php

namespace App\Models;

use App\Models\Traits\HasAccount;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, HasUlids, HasAccount;

    protected $guarded = [];
}
