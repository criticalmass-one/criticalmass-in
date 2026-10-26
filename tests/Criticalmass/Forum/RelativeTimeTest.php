<?php declare(strict_types=1);

namespace Tests\Criticalmass\Forum;

use App\Criticalmass\Forum\RelativeTime;
use PHPUnit\Framework\TestCase;

class RelativeTimeTest extends TestCase
{
    private RelativeTime $relativeTime;
    private \DateTimeImmutable $now;

    protected function setUp(): void
    {
        $this->relativeTime = new RelativeTime();
        $this->now = new \DateTimeImmutable('2026-09-02 12:00:00');
    }

    private function ago(string $modifier): string
    {
        return $this->relativeTime->format($this->now->modify($modifier), $this->now);
    }

    public function testJustNow(): void
    {
        self::assertSame('gerade eben', $this->ago('-30 seconds'));
    }

    public function testMinutes(): void
    {
        self::assertSame('vor 1 Minute', $this->ago('-1 minute'));
        self::assertSame('vor 45 Minuten', $this->ago('-45 minutes'));
    }

    public function testHours(): void
    {
        self::assertSame('vor 1 Stunde', $this->ago('-1 hour'));
        self::assertSame('vor 5 Stunden', $this->ago('-5 hours'));
    }

    public function testYesterday(): void
    {
        self::assertSame('gestern', $this->ago('-1 day'));
    }

    public function testDays(): void
    {
        self::assertSame('vor 3 Tagen', $this->ago('-3 days'));
    }

    public function testOlderThanAWeekFallsBackToTheDate(): void
    {
        // „vor 37 Tagen“ hilft niemandem — ab einer Woche steht wieder das Datum da.
        self::assertSame('am 01.08.2026', $this->ago('-32 days'));
    }

    public function testFutureDatesShowTheirTimestamp(): void
    {
        self::assertSame('03.09.2026, 12:00', $this->ago('+1 day'));
    }

    public function testNullGivesAnEmptyString(): void
    {
        self::assertSame('', $this->relativeTime->format(null));
    }
}
