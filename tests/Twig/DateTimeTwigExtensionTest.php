<?php declare(strict_types=1);

namespace Tests\Twig;

use App\Twig\Extension\DateTimeTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

class DateTimeTwigExtensionTest extends TestCase
{
    private DateTimeTwigExtension $extension;

    protected function setUp(): void
    {
        // 13.10.2026, 20:30 UTC: in Berlin 22:30, in New York 16:30, in Auckland 09:30 am 14.
        $this->extension = new DateTimeTwigExtension(new MockClock('2026-10-13 20:30:00 UTC'));
    }

    public function testAddOneDay(): void
    {
        $dateTime = new \DateTime('2024-01-15 12:00:00');

        $result = $this->extension->add($dateTime, 'P1D');

        $this->assertEquals(new \DateTime('2024-01-16 12:00:00'), $result);
    }

    public function testAddOneHour(): void
    {
        $dateTime = new \DateTime('2024-01-15 12:00:00');

        $result = $this->extension->add($dateTime, 'PT1H');

        $this->assertEquals(new \DateTime('2024-01-15 13:00:00'), $result);
    }

    public function testAddOneMonth(): void
    {
        $dateTime = new \DateTime('2024-01-15 12:00:00');

        $result = $this->extension->add($dateTime, 'P1M');

        $this->assertEquals(new \DateTime('2024-02-15 12:00:00'), $result);
    }

    public function testAddOneYear(): void
    {
        $dateTime = new \DateTime('2024-01-15 12:00:00');

        $result = $this->extension->add($dateTime, 'P1Y');

        $this->assertEquals(new \DateTime('2025-01-15 12:00:00'), $result);
    }

    public function testAddThirtyMinutes(): void
    {
        $dateTime = new \DateTime('2024-01-15 12:00:00');

        $result = $this->extension->add($dateTime, 'PT30M');

        $this->assertEquals(new \DateTime('2024-01-15 12:30:00'), $result);
    }

    public function testOriginalDateTimeIsNotModified(): void
    {
        $dateTime = new \DateTime('2024-01-15 12:00:00');
        $originalTimestamp = $dateTime->getTimestamp();

        $this->extension->add($dateTime, 'P1D');

        $this->assertEquals($originalTimestamp, $dateTime->getTimestamp());
    }

    public function testAddCrossingMidnight(): void
    {
        $dateTime = new \DateTime('2024-01-15 23:30:00');

        $result = $this->extension->add($dateTime, 'PT1H');

        $this->assertEquals(new \DateTime('2024-01-16 00:30:00'), $result);
    }

    public function testAddCrossingMonthBoundary(): void
    {
        $dateTime = new \DateTime('2024-01-31 12:00:00');

        $result = $this->extension->add($dateTime, 'P1D');

        $this->assertEquals(new \DateTime('2024-02-01 12:00:00'), $result);
    }

    public function testAddCrossingYearBoundary(): void
    {
        $dateTime = new \DateTime('2024-12-31 23:00:00');

        $result = $this->extension->add($dateTime, 'PT2H');

        $this->assertEquals(new \DateTime('2025-01-01 01:00:00'), $result);
    }

    public function testGetName(): void
    {
        $this->assertEquals('datetime_extension', $this->extension->getName());
    }

    public function testGetFunctionsReturnsArray(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertIsArray($functions);
        $this->assertNotEmpty($functions);
    }

    public function testTodayIsJudgedInTheGivenTimezone(): void
    {
        $ride = new \DateTime('2026-10-13 10:45:00', new \DateTimeZone('UTC'));

        $this->assertTrue($this->extension->isToday($ride, 'Europe/Berlin'));
        $this->assertTrue($this->extension->isToday($ride, 'America/New_York'));
        $this->assertFalse($this->extension->isToday($ride, 'Pacific/Auckland'));
    }

    public function testAnEveningRideCanAlreadyBeTomorrowInItsCity(): void
    {
        $ride = new \DateTimeImmutable('2026-10-13 23:30:00', new \DateTimeZone('UTC'));

        $this->assertFalse($this->extension->isToday($ride, 'Europe/Berlin'));
        $this->assertTrue($this->extension->isToday($ride, 'UTC'));
        $this->assertTrue($this->extension->isToday($ride, null));
    }

    public function testNoDateIsNeverToday(): void
    {
        $this->assertFalse($this->extension->isToday(null, 'Europe/Berlin'));
    }
}
