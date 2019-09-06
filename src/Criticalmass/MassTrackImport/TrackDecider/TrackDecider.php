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
            $rideResult = 0;

            /** @var VoterInterface $voter */
            foreach ($this->voterList as $voter) {
                $voterResult = $voter->vote($ride, $model);

                if ($voterResult < 0) {
                    break;
                }

                $rideResult += $voterResult;
            }

            $result = new RideResult(
                $ride,
                $rideResult / count($this->voterList)
            );

            $resultList[] = $result;
        }

        if (count($resultList) > 0) {
            return array_pop($resultList);
        }

        return null;
    }
}
