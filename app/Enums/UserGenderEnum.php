<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserGenderEnum: string implements HasColor, HasLabel
{
    case MALE = 'male';
    case FEMALE = 'female';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MALE => 'info',
            self::FEMALE => 'danger',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MALE => 'Laki - Laki',
            self::FEMALE => 'Perempuan',
        };
    }
}
