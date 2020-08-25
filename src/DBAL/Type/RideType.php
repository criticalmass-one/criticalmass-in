<?php declare(strict_types=1);

namespace App\DBAL\Type;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class RideType extends AbstractEnumType
{
    public const CRITICAL_MASS = 'CRITICAL_MASS';
    public const KIDICAL_MASS = 'KIDICAL_MASS';
    public const DEMONSTRATION = 'DEMONSTRATION';
    public const ALLEYCAT = 'ALLEYCAT';
    public const TOUR = 'TOUR';
    public const EVENT = 'EVENT';

    public static $choices = [
        self::CRITICAL_MASS => 'Critical Mass',
        self::KIDICAL_MASS => 'Kidical Mass',
        self::DEMONSTRATION => 'Demonstration',
        self::ALLEYCAT => 'Alleycat',
        self::TOUR => 'Rund- oder Sternfahrt',
        self::EVENT => 'andere Veranstaltung',
    ];
}
