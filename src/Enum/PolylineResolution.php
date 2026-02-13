<?php declare(strict_types=1);

namespace App\Enum;

enum PolylineResolution: int
{
    case COARSE = 100;
    case MEDIUM = 10;
    case FINE = 2;

    public function label(): string
    {
        return match ($this) {
            self::COARSE => '100 m',
            self::MEDIUM => '10 m',
            self::FINE => '2 m',
        };
    }

    public function tolerance(): float
    {
        return match ($this) {
            self::COARSE => 0.001,
            self::MEDIUM => 0.0001,
            self::FINE => 0.00002,
        };
    }
}
