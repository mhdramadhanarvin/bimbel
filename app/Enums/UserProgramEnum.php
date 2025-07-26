<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserProgramEnum: string implements HasLabel
{
    case TNI = 'tni';
    case POLRI = 'polri';
    case KEDINASAN = 'kedinasan';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TNI => 'Bintara TNI',
            self::POLRI => 'Bintara Polri',
            self::KEDINASAN => 'Sekolah Kedinasan',
        };
    }
}
