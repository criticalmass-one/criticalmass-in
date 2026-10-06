<?php declare(strict_types=1);

namespace App\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\Exception\InvalidFormat;

/**
 * Speichert Zeitstempel grundsaetzlich in UTC und liest sie auch so zurueck.
 *
 * Bis auf die Zeitzone verhaelt sich der Typ wie {@see DateTimeType} — inklusive
 * der Rueckfallebene fuer Werte, die nicht auf das Format der Plattform passen.
 * PostgreSQL etwa liefert Zeitstempel mit Sekundenbruchteilen, an denen ein
 * strenges createFromFormat('Y-m-d H:i:s') scheitert.
 */
class UTCDateTimeType extends DateTimeType
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

        $format = $platform->getDateTimeFormatString();

        $converted = \DateTime::createFromFormat($format, $value, self::getUtc());

        if (false !== $converted) {
            return $converted;
        }

        try {
            return new \DateTime($value, self::getUtc());
        } catch (\Exception $exception) {
            throw InvalidFormat::new($value, static::class, $format, $exception);
        }
    }
}
