<?php declare(strict_types=1);

namespace Tests\DBAL\Type;

use App\DBAL\Type\UTCDateTimeType;
use App\DBAL\Type\UTCDateType;
use App\DBAL\Type\UTCTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

/**
 * Diese drei Typen liegen auf dem Lesepfad jeder Entity mit Zeitangabe. Getestet
 * wurden sie bis dahin nie — und enthielten mit getName() und
 * ConversionException::conversionFailedFormat() zwei Aufrufe, die es in DBAL 4
 * nicht mehr gibt.
 */
class UTCTypesTest extends TestCase
{
    private AbstractPlatform $mariaDb;
    private AbstractPlatform $postgres;

    protected function setUp(): void
    {
        $this->mariaDb = new MariaDBPlatform();
        $this->postgres = new PostgreSQLPlatform();
    }

    private function type(string $name, string $class): Type
    {
        if (!Type::hasType($name)) {
            Type::addType($name, $class);
        }

        return Type::getType($name);
    }

    private function dateTimeType(): UTCDateTimeType
    {
        $type = $this->type('test_utc_datetime', UTCDateTimeType::class);
        self::assertInstanceOf(UTCDateTimeType::class, $type);

        return $type;
    }

    private function dateType(): UTCDateType
    {
        $type = $this->type('test_utc_date', UTCDateType::class);
        self::assertInstanceOf(UTCDateType::class, $type);

        return $type;
    }

    private function timeType(): UTCTimeType
    {
        $type = $this->type('test_utc_time', UTCTimeType::class);
        self::assertInstanceOf(UTCTimeType::class, $type);

        return $type;
    }

    public function testDatabaseValueIsReadAsUtc(): void
    {
        $value = $this->dateTimeType()->convertToPHPValue('2026-09-04 17:30:00', $this->mariaDb);

        self::assertInstanceOf(\DateTime::class, $value);
        self::assertSame('UTC', $value->getTimezone()->getName());
        self::assertSame('2026-09-04 17:30:00', $value->format('Y-m-d H:i:s'));
    }

    public function testLocalTimeIsWrittenBackAsUtc(): void
    {
        $berlin = new \DateTime('2026-09-04 19:30:00', new \DateTimeZone('Europe/Berlin'));

        self::assertSame(
            '2026-09-04 17:30:00',
            $this->dateTimeType()->convertToDatabaseValue($berlin, $this->mariaDb)
        );
    }

    /**
     * PostgreSQL haengt an Zeitstempel Sekundenbruchteile, auf die das
     * Plattformformat 'Y-m-d H:i:s' nicht passt. Ohne Rueckfallebene scheitert
     * hier jeder Lesevorgang — genau das waere beim Plattformwechsel passiert.
     */
    public function testTimestampWithMicrosecondsIsAccepted(): void
    {
        $value = $this->dateTimeType()->convertToPHPValue('2026-09-04 17:30:00.323502', $this->postgres);

        self::assertInstanceOf(\DateTime::class, $value);
        self::assertSame('UTC', $value->getTimezone()->getName());
        self::assertSame('2026-09-04 17:30:00', $value->format('Y-m-d H:i:s'));
    }

    public function testUnparsableTimestampNamesTheTypeAndTheFormat(): void
    {
        $this->expectException(InvalidFormat::class);
        $this->expectExceptionMessage(UTCDateTimeType::class);

        $this->dateTimeType()->convertToPHPValue('kein Zeitstempel', $this->mariaDb);
    }

    /**
     * Ein bereits umgewandelter Wert wird unveraendert durchgereicht — dieselbe
     * Instanz, keine Kopie und keine erneute Zeitzonenrechnung.
     */
    public function testAnExistingDateTimeIsPassedThroughUntouched(): void
    {
        $existing = new \DateTime('2026-09-04 17:30:00', new \DateTimeZone('Europe/Berlin'));

        self::assertSame($existing, $this->dateTimeType()->convertToPHPValue($existing, $this->mariaDb));
        self::assertSame($existing, $this->dateType()->convertToPHPValue($existing, $this->mariaDb));
        self::assertSame($existing, $this->timeType()->convertToPHPValue($existing, $this->mariaDb));
        self::assertSame('Europe/Berlin', $existing->getTimezone()->getName());
    }

    /**
     * Der eigentliche Fehler im Datumstyp: ohne '!' im Format uebernimmt
     * createFromFormat die aktuelle Uhrzeit, ein gelesenes Datum trug damit die
     * Tageszeit des Lesezeitpunkts.
     */
    public function testDateIsReadAtMidnight(): void
    {
        $value = $this->dateType()->convertToPHPValue('2026-09-04', $this->mariaDb);

        self::assertInstanceOf(\DateTime::class, $value);
        self::assertSame('2026-09-04 00:00:00', $value->format('Y-m-d H:i:s'));
        self::assertSame('UTC', $value->getTimezone()->getName());
    }

    public function testUnparsableDateNamesTheType(): void
    {
        $this->expectException(InvalidFormat::class);
        $this->expectExceptionMessage(UTCDateType::class);

        $this->dateType()->convertToPHPValue('kein Datum', $this->mariaDb);
    }

    public function testTimeIsReadWithoutADate(): void
    {
        $value = $this->timeType()->convertToPHPValue('19:30:00', $this->mariaDb);

        self::assertInstanceOf(\DateTime::class, $value);
        self::assertSame('19:30:00', $value->format('H:i:s'));
        self::assertSame('1970-01-01', $value->format('Y-m-d'));
        self::assertSame('UTC', $value->getTimezone()->getName());
    }

    public function testUnparsableTimeNamesTheType(): void
    {
        $this->expectException(InvalidFormat::class);
        $this->expectExceptionMessage(UTCTimeType::class);

        $this->timeType()->convertToPHPValue('keine Uhrzeit', $this->mariaDb);
    }
}
