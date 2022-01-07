<?php

namespace App\Enums;

use BenSampo\Enum\Enum;


final class SexType extends Enum
{
    const MALE = 0;
    const FEMALE = 1;

    public static function getDescription($value): string
    {
        if ($value === self::MALE) {
            return 'Laki-laki';
        }
        return 'Perempuan';
    }
}
