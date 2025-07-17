<?php

namespace App\Enums;

use App\Enums\Traits\EnumTrait;
use Illuminate\Support\Str;

/**
 * @method static int MANUALLY()
 * @method static int MONTH_DAY()
 */
enum ClosedOption: int
{
    use EnumTrait;

    case MANUALLY = 1;
    case MONTH_DAY = 2;

    public function title()
    {
        return match ($this) {
            default => Str::headline(Str::lower($this->name)),
        };
    }
}
