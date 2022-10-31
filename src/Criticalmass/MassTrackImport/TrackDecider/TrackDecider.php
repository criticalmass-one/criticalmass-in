<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\TrackDecider;

use App\Criticalmass\MassTrackImport\Voter\VoterInterface;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use Doctrine\Persistence\ManagerRegistry;

class TrackDecider implements TrackDeciderInterface
{
    final const THRESHOLD = 0.75;

    /** @var array $voterList */
    protected $voterList = [];

    /** @var bool $debug */
    protected $debug = false;

    public function __construct(protected ManagerRegistry $registry)
    {
    }

    public function addVoter(VoterInterface $voter): TrackDeciderInterface
    {
        $this->voterList[] = $voter;

        return $this;
    }

    public function decide(TrackImportCandidate $trackImportCandidate): ?RideResult
    {
        $rides = $this->registry->getRepository(Ride::class)->findByDate($trackImportCandidate->getStartDateTime());

        $resultList = [];

        foreach ($rides as $ride) {
            if ($rideResult = $this->vote($ride, $trackImportCandidate)) {
                $resultList[] = $rideResult;
            }
        }

        return $this->handleResultList($resultList);
    }

    protected function vote(Ride $ride, TrackImportCandidate $trackImportCandidate): ?RideResult
    {
        $rideResult = new RideResult($ride, $trackImportCandidate);

        /** @var VoterInterface $voter */
        foreach ($this->voterList as $voter) {
            $voterResult = $voter->vote($ride, $trackImportCandidate);

            if ($voterResult < 0 && !$this->debug) {
                return null;
            }

            $rideResult->addVoterResult($voter, $voterResult);
        }

        $voterResultSum = 0;

        foreach ($rideResult->getVoterResults() as $voterName => $voterResult) {
            $voterResultSum += $voterResult;
        }

        $rideResult->setResult($voterResultSum / count($this->voterList));

        return $rideResult;
    }

    protected function handleResultList(array $resultList): ?RideResult
    {
        if (count($resultList) > 0) {
            usort($resultList, fn(RideResult $rideResult1, RideResult $rideResult2): int => $rideResult2->getResult() <=> $rideResult1->getResult());

            /** @var RideResult $bestResult */
            $bestResult = array_shift($resultList);

            $bestResult->getActivity()->setRide($bestResult->getRide());

            if ($bestResult->getResult() >= self::THRESHOLD) {
                $bestResult->setMatch(true);

                return $bestResult;
            }

            if ($this->debug) {
                return $bestResult;
            }
        }

        return null;
    }
}
