<?php

namespace App\Models;

use App\Models\Traits\HasAccount;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasAccount, HasFactory, HasUlids;

    protected $guarded = [];
}
