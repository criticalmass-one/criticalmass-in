<?php declare(strict_types=1);

namespace App\Enum;

enum RideTypeEnum: string
{
    case CRITICAL_MASS = 'CRITICAL_MASS';
    case KIDICAL_MASS = 'KIDICAL_MASS';
    case NIGHT_RIDE = 'NIGHT_RIDE';
    case LUNCH_RIDE = 'LUNCH_RIDE';
    case DAWN_RIDE = 'DAWN_RIDE';
    case DUSK_RIDE = 'DUSK_RIDE';
    case DEMONSTRATION = 'DEMONSTRATION';
    case ALLEYCAT = 'ALLEYCAT';
    case TOUR = 'TOUR';
    case EVENT = 'EVENT';

    public function label(): string
    {
        return match($this) {
            self::CRITICAL_MASS => 'Critical Mass',
            self::KIDICAL_MASS => 'Kidical Mass',
            self::NIGHT_RIDE => 'Nightride',
            self::LUNCH_RIDE => 'Lunchride',
            self::DAWN_RIDE => 'Dawn Ride',
            self::DUSK_RIDE => 'Dusk Ride',
            self::DEMONSTRATION => 'Demonstration',
            self::ALLEYCAT => 'Alleycat',
            self::TOUR => 'Rund- oder Sternfahrt',
            self::EVENT => 'andere Veranstaltung',
        };
    }

    public static function choices(): array
    {
        return array_combine(
            array_map(fn($case) => $case->value, self::cases()),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}
