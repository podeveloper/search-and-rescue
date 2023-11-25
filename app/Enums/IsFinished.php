<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum IsFinished: string implements HasColor, HasIcon, HasLabel
{
    case UNFINISHED = '0';
    case FINISHED = '1';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UNFINISHED => 'Unfinished',
            self::FINISHED => 'Finished',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UNFINISHED => 'danger',
            self::FINISHED => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::UNFINISHED => false,
            self::FINISHED => 'heroicon-o-check',
        };
    }
}
