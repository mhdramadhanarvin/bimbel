<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserReligionEnum: string implements HasLabel
{
    case ISLAM = 'islam';
    case KATOLIK = 'katolik';
    case PROTESTAN = 'protestan';
    case BUDDHA = 'buddha';
    case HINDU = 'hindu';
    case KONGHUCU = 'konghucu';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ISLAM => 'Islam',
            self::KATOLIK => 'Kristen Katolik',
            self::PROTESTAN => 'Kristen Protestan',
            self::BUDDHA => 'Buddha',
            self::HINDU => 'Hindu',
            self::KONGHUCU => 'Konghucu',
        };
    }
}
