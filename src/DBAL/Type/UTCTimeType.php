<?php declare(strict_types=1);

namespace App\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\TimeType;

/**
 * Speichert Uhrzeiten grundsaetzlich in UTC und liest sie auch so zurueck.
 *
 * Verhaelt sich ansonsten wie {@see TimeType}.
 */
class UTCTimeType extends TimeType
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

        $format = $platform->getTimeFormatString();

        $converted = \DateTime::createFromFormat('!' . $format, $value, self::getUtc());

        if (false !== $converted) {
            return $converted;
        }

        throw InvalidFormat::new($value, static::class, $format);
    }
}
