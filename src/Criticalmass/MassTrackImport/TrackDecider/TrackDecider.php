<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\TrackDecider;

use App\Criticalmass\MassTrackImport\Voter\VoterInterface;
use App\Entity\Ride;
use App\Entity\TrackImportProposal;
use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TrackDecider implements TrackDeciderInterface
{
    const THRESHOLD = 0.75;

    /** @var array $voterList */
    protected $voterList = [];

    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var bool $debug */
    protected $debug = false;

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    public function __construct(RegistryInterface $registry, TokenStorageInterface $tokenStorage)
    {
        $this->registry = $registry;
        $this->tokenStorage = $tokenStorage;
    }

    public function addVoter(VoterInterface $voter): TrackDeciderInterface
    {
        $this->voterList[] = $voter;

        return $this;
    }

    public function decide(TrackImportProposal $trackImportProposal): ?RideResult
    {
        $rides = $this->registry->getRepository(Ride::class)->findByDate($trackImportProposal->getStartDateTime());

        $resultList = [];

        foreach ($rides as $ride) {
            if ($rideResult = $this->vote($ride, $trackImportProposal)) {
                $resultList[] = $rideResult;
            }
        }

        return $this->handleResultList($resultList);
    }

    protected function vote(Ride $ride, TrackImportProposal $trackImportProposal): ?RideResult
    {
        $rideResult = new RideResult($ride, $trackImportProposal);

        /** @var VoterInterface $voter */
        foreach ($this->voterList as $voter) {
            $voterResult = $voter->vote($ride, $trackImportProposal);

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
            usort($resultList, function (RideResult $rideResult1, RideResult $rideResult2): int {
                if ($rideResult1->getResult() === $rideResult2->getResult()) {
                    return 0;
                }

                return $rideResult1->getResult() > $rideResult2->getResult() ? -1 : 1;
            });

            /** @var RideResult $bestResult */
            $bestResult = array_shift($resultList);

            $bestResult->getActivity()
                ->setRide($bestResult->getRide())
                ->setUser($this->getUser());

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

    protected function getUser(): User
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
