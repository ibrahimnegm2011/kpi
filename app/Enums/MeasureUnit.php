<?php

namespace App\Enums;

use App\Enums\Traits\EnumTrait;
use Illuminate\Support\Str;

/**
 * @method static int NUMBER()
 * @method static int AMOUNT()
 */
enum MeasureUnit: string
{
    use EnumTrait;

    case NUMBER = 'number';
    case AMOUNT = 'amount';

    public function symbol()
    {
        return match ($this) {
            self::AMOUNT => 'SAR',
            default => '',
        };
    }

    public function title()
    {
        return match ($this) {
            default => Str::title($this->name).($this->symbol() ? ' ('.$this->symbol().')' : ''),
        };
    }
}
