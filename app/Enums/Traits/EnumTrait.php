<?php

namespace App\Enums\Traits;

use Illuminate\Support\Arr;

trait EnumTrait
{
    public static function values()
    {
        return Arr::map(self::cases(), function ($case) {
            return $case->value;
        });
    }

    public static function names()
    {
        return Arr::map(self::cases(), function ($case) {
            return $case->name;
        });
    }

    public static function __callStatic(string $name, array $arguments)
    {
        $case = Arr::first(self::cases(), fn ($case) => $case->name == $name);

        if ($case) {
            return $case->value;
        }

        throw new \InvalidArgumentException("Invalid method: {$name}");
    }
}
