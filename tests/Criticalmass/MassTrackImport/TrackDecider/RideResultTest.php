<?php declare(strict_types=1);

namespace Tests\Criticalmass\MassTrackImport\TrackDecider;

use App\Criticalmass\MassTrackImport\TrackDecider\RideResult;
use App\Criticalmass\MassTrackImport\Voter\DurationVoter;
use App\Criticalmass\MassTrackImport\Voter\NameVoter;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RideResultTest extends TestCase
{
    #[Test]
    public function voterResultsAreKeyedByVoterShortName(): void
    {
        $result = new RideResult(new Ride(), new TrackImportCandidate());

        $result
            ->addVoterResult(new NameVoter(), 0.8)
            ->addVoterResult(new DurationVoter(), 0.5);

        self::assertSame(['NameVoter' => 0.8, 'DurationVoter' => 0.5], $result->getVoterResults());
    }

    #[Test]
    public function addingTheSameVoterTwiceOverwritesItsResult(): void
    {
        $result = new RideResult(new Ride(), new TrackImportCandidate());

        $result
            ->addVoterResult(new NameVoter(), 0.8)
            ->addVoterResult(new NameVoter(), 0.1);

        self::assertSame(['NameVoter' => 0.1], $result->getVoterResults());
    }

    #[Test]
    public function isNotAMatchUntilMarked(): void
    {
        $ride = new Ride();
        $candidate = new TrackImportCandidate();
        $result = new RideResult($ride, $candidate);

        self::assertFalse($result->isMatch());
        self::assertSame($ride, $result->getRide());
        self::assertSame($candidate, $result->getActivity());

        $result->setMatch(true)->setResult(0.9);

        self::assertTrue($result->isMatch());
        self::assertSame(0.9, $result->getResult());
    }
}
