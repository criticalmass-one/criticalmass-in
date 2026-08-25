<?php declare(strict_types=1);

namespace Tests\Criticalmass\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Voter\DateTimeVoter;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DateTimeVoterTest extends TestCase
{
    /**
     * @return iterable<string, array{0: string, 1: float}>
     */
    public static function offsets(): iterable
    {
        yield 'exact start' => ['2024-05-31 19:00:00', 1.0];
        yield '10 minutes late' => ['2024-05-31 19:10:00', 1.0];
        yield '15 minutes early' => ['2024-05-31 18:45:00', 1.0];
        yield '16 minutes late' => ['2024-05-31 19:16:00', 0.9];
        yield '30 minutes late' => ['2024-05-31 19:30:00', 0.9];
        yield '45 minutes late' => ['2024-05-31 19:45:00', 0.8];
        yield '90 minutes early' => ['2024-05-31 17:30:00', 0.7];
        yield '3 hours late' => ['2024-05-31 22:00:00', 0.5];
        yield '4 hours early' => ['2024-05-31 15:00:00', 0.3];
        yield 'same day but far apart' => ['2024-05-31 06:00:00', 0.25];
        yield 'next day' => ['2024-06-01 19:00:00', -1.0];
        yield 'previous day just before midnight' => ['2024-05-30 23:59:00', -1.0];
    }

    #[Test]
    #[DataProvider('offsets')]
    public function scoreDependsOnDistanceToRideStart(string $candidateStart, float $expected): void
    {
        $ride = (new Ride())->setDateTime(new \DateTime('2024-05-31 19:00:00'));
        $candidate = (new TrackImportCandidate())->setStartDateTime(new \DateTime($candidateStart));

        self::assertEqualsWithDelta($expected, (new DateTimeVoter())->vote($ride, $candidate), 0.0001);
    }
}
