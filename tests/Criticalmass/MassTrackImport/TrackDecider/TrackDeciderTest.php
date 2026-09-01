<?php declare(strict_types=1);

namespace Tests\Criticalmass\MassTrackImport\TrackDecider;

use App\Criticalmass\MassTrackImport\TrackDecider\TrackDecider;
use App\Criticalmass\MassTrackImport\Voter\VoterInterface;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use App\Repository\RideRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

/**
 * #1403: Orchestrierung des Upload-Matchings — Schwellenwert (0.75), Auswahl des
 * besten Rides, Veto eines Voters und Parken unterhalb der Schwelle.
 */
final class TrackDeciderTest extends TestCase
{
    private static int $voterCounter = 0;

    /**
     * @param list<Ride> $rides
     */
    private function decider(array $rides): TrackDecider
    {
        $rideRepository = $this->createMock(RideRepository::class);
        $rideRepository->method('findByDate')->willReturn($rides);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(Ride::class)->willReturn($rideRepository);

        return new TrackDecider($registry);
    }

    /**
     * RideResult::addVoterResult keyt nach dem Klassen-Kurznamen des Voters —
     * deshalb braucht jeder Mock-Voter eine eigene Klasse (eindeutiger Name).
     */
    private function voter(float $score): VoterInterface
    {
        $voter = $this->getMockBuilder(VoterInterface::class)
            ->setMockClassName('TestVoter' . (++self::$voterCounter))
            ->getMock();
        $voter->method('vote')->willReturn($score);

        return $voter;
    }

    private function candidate(): TrackImportCandidate
    {
        $candidate = new TrackImportCandidate();
        $candidate->setStartDateTime(new \DateTime('2026-09-01 19:00:00'));

        return $candidate;
    }

    public function testMatchAboveThresholdReturnsRideResult(): void
    {
        $ride = new Ride();
        $candidate = $this->candidate();

        $decider = $this->decider([$ride])
            ->addVoter($this->voter(1.0))
            ->addVoter($this->voter(0.8));

        $result = $decider->decide($candidate);

        self::assertNotNull($result);
        self::assertTrue($result->isMatch());
        self::assertSame($ride, $result->getRide());
        // Durchschnitt (1.0 + 0.8) / 2 = 0.9 ≥ 0.75.
        self::assertEqualsWithDelta(0.9, $result->getResult(), 0.0001);
        // Der Kandidat bekommt den gematchten Ride zugewiesen.
        self::assertSame($ride, $candidate->getRide());
    }

    public function testBelowThresholdIsParked(): void
    {
        $decider = $this->decider([new Ride()])
            ->addVoter($this->voter(0.5))
            ->addVoter($this->voter(0.7));

        // Durchschnitt 0.6 < 0.75 → kein Match → null (geparkt).
        self::assertNull($decider->decide($this->candidate()));
    }

    public function testNegativeVoterVetoesRide(): void
    {
        $decider = $this->decider([new Ride()])
            ->addVoter($this->voter(1.0))
            ->addVoter($this->voter(-1.0));

        // Ein negatives Votum vetoed den Ride → keine Kandidaten → null.
        self::assertNull($decider->decide($this->candidate()));
    }

    public function testPicksBestScoringRide(): void
    {
        $weakRide = new Ride();
        $strongRide = new Ride();

        // Voter bewertet weakRide mit 0.8, strongRide mit 1.0 (per Map über Ride-Objekt).
        $voter = $this->getMockBuilder(VoterInterface::class)
            ->setMockClassName('TestVoter' . (++self::$voterCounter))
            ->getMock();
        $voter->method('vote')->willReturnCallback(
            static fn (Ride $ride): float => $ride === $strongRide ? 1.0 : 0.8,
        );

        $result = $this->decider([$weakRide, $strongRide])->addVoter($voter)->decide($this->candidate());

        self::assertNotNull($result);
        self::assertSame($strongRide, $result->getRide());
    }

    public function testNoRideOnDateReturnsNull(): void
    {
        $decider = $this->decider([])->addVoter($this->voter(1.0));

        self::assertNull($decider->decide($this->candidate()));
    }
}
