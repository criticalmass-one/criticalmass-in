<?php declare(strict_types=1);

namespace App\Tests\Twig\Extension;

use App\Twig\Extension\DateTimeTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

class DateTimeTwigExtensionTest extends TestCase
{
    private DateTimeTwigExtension $extension;

    protected function setUp(): void
    {
        ClockMock::register(DateTimeTwigExtension::class);
        // 13.10.2026, 20:30 UTC: in Berlin 22:30, in New York 16:30, in Auckland 09:30 am 14.
        ClockMock::withClockMock(strtotime('2026-10-13 20:30:00 UTC'));

        $this->extension = new DateTimeTwigExtension();
    }

    protected function tearDown(): void
    {
        ClockMock::withClockMock(false);
    }

    public function testTodayIsJudgedInTheGivenTimezone(): void
    {
        $ride = new \DateTime('2026-10-13 10:45:00', new \DateTimeZone('UTC'));

        self::assertTrue($this->extension->isToday($ride, 'Europe/Berlin'));
        self::assertTrue($this->extension->isToday($ride, 'America/New_York'));
        self::assertFalse($this->extension->isToday($ride, 'Pacific/Auckland'));
    }

    public function testAnEveningRideCanAlreadyBeTomorrowInItsCity(): void
    {
        $ride = new \DateTimeImmutable('2026-10-13 23:30:00', new \DateTimeZone('UTC'));

        self::assertFalse($this->extension->isToday($ride, 'Europe/Berlin'));
        self::assertTrue($this->extension->isToday($ride, 'UTC'));
        self::assertTrue($this->extension->isToday($ride, null));
    }

    public function testNoDateIsNeverToday(): void
    {
        self::assertFalse($this->extension->isToday(null, 'Europe/Berlin'));
    }
}
