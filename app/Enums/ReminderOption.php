<?php

namespace App\Enums;

use App\Enums\Traits\EnumTrait;
use Illuminate\Support\Str;

/**
 * @method static int BASIC()
 * @method static int EVERY_DAY()
 * @method static int EVERY_WEEK()
 */
enum ReminderOption: int
{
    use EnumTrait;

    case BASIC = 1;
    case EVERY_DAY = 2;
    case EVERY_WEEK = 3;

    public function title()
    {
        return match ($this) {
            default => Str::headline(Str::lower($this->name)),
        };
    }
}
