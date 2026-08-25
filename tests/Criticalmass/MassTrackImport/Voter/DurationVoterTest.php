<?php declare(strict_types=1);

namespace Tests\Criticalmass\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Voter\DurationVoter;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DurationVoterTest extends TestCase
{
    /**
     * @return iterable<string, array{0: int, 1: float}>
     */
    public static function durations(): iterable
    {
        yield 'zero' => [0, 0.0];
        yield 'ten minutes' => [10 * 60, 0.0];
        yield 'exactly 15 minutes is still too short' => [15 * 60, 0.0];
        yield 'just over 15 minutes' => [15 * 60 + 1, 0.5];
        yield 'exactly 45 minutes is plausible only' => [45 * 60, 0.5];
        yield 'just over 45 minutes is typical' => [45 * 60 + 1, 0.75];
        yield 'two hours' => [2 * 3600, 0.75];
        yield 'just under three hours' => [3 * 3600 - 1, 0.75];
        yield 'exactly three hours' => [3 * 3600, 0.5];
        yield 'just under six hours' => [6 * 3600 - 1, 0.5];
        yield 'six hours' => [6 * 3600, 0.0];
        yield 'a whole day' => [24 * 3600, 0.0];
    }

    #[Test]
    #[DataProvider('durations')]
    public function scoreReflectsTypicalRideLength(int $elapsedSeconds, float $expected): void
    {
        $candidate = (new TrackImportCandidate())->setElapsedTime($elapsedSeconds);

        self::assertSame($expected, (new DurationVoter())->vote(new Ride(), $candidate));
    }
}
