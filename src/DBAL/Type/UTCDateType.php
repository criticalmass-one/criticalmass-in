<?php declare(strict_types=1);

namespace App\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\Exception\InvalidFormat;

/**
 * Speichert Datumswerte grundsaetzlich in UTC und liest sie auch so zurueck.
 *
 * Das Ausrufezeichen vor dem Format ist wesentlich: ohne es uebernimmt
 * createFromFormat fuer die nicht im Format genannten Felder die aktuelle
 * Uhrzeit, ein gelesenes Datum traegt dann die Tageszeit des Lesezeitpunkts
 * statt Mitternacht. {@see DateType} macht es genauso.
 */
class UTCDateType extends DateType
{
    private static ?\DateTimeZone $utc = null;

    protected static function getUtc(): \DateTimeZone
    {
        if (self::$utc === null) {
            self::$utc = new \DateTimeZone('UTC');
        }
        return self::$utc;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof \DateTime) {
            $value->setTimezone(self::getUtc());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?\DateTime
    {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        $format = $platform->getDateFormatString();

        $converted = \DateTime::createFromFormat('!' . $format, $value, self::getUtc());

        if (false !== $converted) {
            return $converted;
        }

        throw InvalidFormat::new($value, static::class, $format);
    }
}
