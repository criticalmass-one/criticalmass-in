<?php declare(strict_types=1);

namespace Tests\Criticalmass\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Voter\TypeVoter;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use PHPUnit\Framework\TestCase;

class TypeVoterTest extends TestCase
{
    public function testStravaRideTypePasses(): void
    {
        $candidate = (new TrackImportCandidate())->setType('Ride');

        self::assertGreaterThan(0, (new TypeVoter())->vote($this->createMock(Ride::class), $candidate));
    }

    public function testStravaNonRideTypeIsDisqualified(): void
    {
        $candidate = (new TrackImportCandidate())->setType('Run');

        self::assertLessThan(0, (new TypeVoter())->vote($this->createMock(Ride::class), $candidate));
    }

    public function testUploadSourceIsNeverDisqualifiedRegardlessOfType(): void
    {
        $candidate = (new TrackImportCandidate())
            ->setSource(TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD)
            ->setType('Run');

        self::assertGreaterThanOrEqual(0, (new TypeVoter())->vote($this->createMock(Ride::class), $candidate));
    }
}
