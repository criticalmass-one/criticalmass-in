<?php declare(strict_types=1);

namespace App\DBAL\Type;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class RideType extends AbstractEnumType
{
    public const CRITICAL_MASS = 'CRITICAL_MASS';
    public const KIDICAL_MASS = 'KIDICAL_MASS';
    public const NIGHT_RIDE = 'NIGHT_RIDE';
    public const LUNCH_RIDE = 'LUNCH_RIDE';
    public const DAWN_RIDE = 'DAWN_RIDE';
    public const DUSK_RIDE = 'DUSK_RIDE';
    public const DEMONSTRATION = 'DEMONSTRATION';
    public const ALLEYCAT = 'ALLEYCAT';
    public const TOUR = 'TOUR';
    public const EVENT = 'EVENT';

    public static array $choices = [
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
    ];
}
