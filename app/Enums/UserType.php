<?php

namespace App\Enums;

use App\Enums\Traits\EnumTrait;
use Illuminate\Support\Str;

/**
 * @method static string ADMIN()
 * @method static string ACCOUNT()
 * @method static string AGENT()
 */
enum UserType: string
{
    use EnumTrait;

    case ADMIN = 'admin';
    case ACCOUNT = 'account';
    case AGENT = 'agent';

    public function title()
    {
        return match ($this) {
            default => Str::title($this->name),
        };
    }
}
