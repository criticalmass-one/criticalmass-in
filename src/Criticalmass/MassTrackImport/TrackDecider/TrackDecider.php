<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\TrackDecider;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Criticalmass\MassTrackImport\Voter\VoterInterface;
use App\Entity\Ride;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TrackDecider implements TrackDeciderInterface
{
    /** @var array $voterList */
    protected $voterList = [];

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function addVoter(VoterInterface $voter): TrackDeciderInterface
    {
        $this->voterList[] = $voter;

        return $this;
    }

    public function decide(StravaActivityModel $model): ?RideResult
    {
        $rides = $this->registry->getRepository(Ride::class)->findByDate($model->getStartDateTime());

        $resultList = [];

        foreach ($rides as $ride) {
            $rideResult = new RideResult($ride, $model);

            /** @var VoterInterface $voter */
            foreach ($this->voterList as $voter) {
                $voterResult = $voter->vote($ride, $model);

                if ($voterResult < 0) {
                    break;
                }

                $rideResult->addVoterResult($voter, $voterResult);
            }

            $voterResultSum = 0;

            foreach ($rideResult->getVoterResults() as $voterName => $voterResult) {
                $voterResultSum += $voterResult;
            }

            $rideResult->setResult($voterResultSum / count($this->voterList));

            $resultList[] = $rideResult;
        }

        if (count($resultList) > 0) {
            usort($resultList, function (RideResult $rideResult1, RideResult $rideResult2): int {
                if ($rideResult1->getResult() === $rideResult2->getResult()) {
                    return 0;
                }

                return $rideResult1->getResult() > $rideResult2->getResult() ? -1 : 1;
            });

            return array_shift($resultList);
        }

        return null;
    }
}
