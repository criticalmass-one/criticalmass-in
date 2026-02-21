<?php declare(strict_types=1);

namespace App\Enum;

enum RideDisabledReasonEnum: string
{
    case DUPLICATE = 'DUPLICATE';
    case WRONG_WEBSITE_HANDLING = 'WRONG_WEBSITE_HANDLING';
    case WRONG_AUTO_GENERATION = 'WRONG_AUTO_GENERATION';
    case CANCELLED_WEATHER = 'CANCELLED_WEATHER';
    case CANCELLED_PARTICIPANTS = 'CANCELLED_PARTICIPANTS';
    case CANCELLED_AUTHORITIES = 'CANCELLED_AUTHORITIES';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match($this) {
            self::DUPLICATE => 'diese Tour ist ein Duplikat einer anderen Tour, die in der gleichen Woche stattfindet',
            self::WRONG_WEBSITE_HANDLING => 'diese Tour wurde infolge einer manuellen Fehlbedienung der Webseite angelegt',
            self::WRONG_AUTO_GENERATION => 'diese Tour wurde automatisch generiert, obwohl sie nicht stattindet',
            self::CANCELLED_WEATHER => 'diese Tour fand aufgrund der Witterungsbedingungen nicht statt',
            self::CANCELLED_PARTICIPANTS => 'diese Tour wurde aufgrund mangelnder Teilnehmerzahlen abgesagt',
            self::CANCELLED_AUTHORITIES => 'diese Tour wurde von der Polizei oder anderen Behörden untersagt',
            self::CANCELLED => 'diese Tour wurde abgesagt',
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
