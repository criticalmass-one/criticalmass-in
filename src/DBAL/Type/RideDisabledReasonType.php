<?php declare(strict_types=1);

namespace App\DBAL\Type;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class RideDisabledReasonType extends AbstractEnumType
{
    public const DUPLICATE = 'DUPLICATE';
    public const WRONG_WEBSITE_HANDLING = 'WRONG_WEBSITE_HANDLING';
    public const WRONG_AUTO_GNERATION = 'WRONG_AUTO_GNERATION';
    public const CANCELLED_WEATHER = 'CANCELLED_WEATHER';
    public const CANCELLED_PARTICIPANTS = 'CANCELLED_WEATHER';
    public const CANCELLED_AUTHORITIES = 'CANCELLED_AUTHORITIES';
    public const CANCELLED = 'CANCELLED';

    public static $choices = [
        self::DUPLICATE => 'diese Tour ist ein Duplikat einer anderen Tour, die in der gleichen Woche stattfindet',
        self::WRONG_WEBSITE_HANDLING => 'diese Tour wurde infolge einer manuellen Fehlbedienung der Webseite angelegt',
        self::WRONG_AUTO_GNERATION => 'diese Tour wurde automatisch generiert, obwohl sie nicht stattindet',
        self::CANCELLED_WEATHER => 'diese Tour fand aufgrund der Witterungsbedingungen nicht statt',
        self::CANCELLED_PARTICIPANTS => 'diese Tour wurde aufgrund mangelnder Teilnehmerzahlen abgesagt',
        self::CANCELLED_AUTHORITIES => 'diese Tour wurde von der Polizei oder anderen Behörden untersagt',
        self::CANCELLED => 'diese Tour wurde abgesagt',
    ];
}