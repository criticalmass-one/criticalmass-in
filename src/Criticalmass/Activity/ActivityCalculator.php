<?php declare(strict_types=1);

namespace App\Criticalmass\Activity;

use App\Criticalmass\Activity\Aspect\AspectInterface;
use App\Criticalmass\Activity\Aspect\EstimationAspect;
use App\Criticalmass\Activity\Aspect\MonthlyRideAspect;
use App\Criticalmass\Activity\Aspect\ParticipationAspect;
use App\Criticalmass\Activity\Aspect\PhotoAspect;
use App\Criticalmass\Activity\Aspect\TrackAspect;
use App\Entity\City;
use App\Entity\Ride;
use Carbon\Carbon;
use Doctrine\Persistence\ManagerRegistry;

class ActivityCalculator implements ActivityCalculatorInterface
{
    protected ManagerRegistry $managerRegistry;
    protected array $aspectList = [];

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;

        $this->aspectList[] = new EstimationAspect();
        $this->aspectList[] = new ParticipationAspect();
        $this->aspectList[] = new PhotoAspect();
        $this->aspectList[] = new TrackAspect();
    }

    public function calculate(City $city): float
    {
        $startDateTime = (new Carbon())->subYear();
        $endDateTime = new Carbon();

        $rideList = $this->managerRegistry->getRepository(Ride::class)->findRides($startDateTime, $endDateTime, $city);
        $rideDataList = [];

        if (0 === count($rideList)) {
            return 0;
        }

        /** @var Ride $ride */
        foreach ($rideList as $ride) {
            $rideData = new RideData($ride);

            /** @var AspectInterface $aspect */
            foreach ($this->aspectList as $aspect) {
                $result = $aspect->calculate($rideData);
                $rideData->addResult($result);
            }

            $rideData->setResult($rideData->getResult() / count($this->aspectList));

            $rideDataList[] = $rideData;
        }

        $result = 0;

        /** @var RideData $rideData */
        foreach ($rideDataList as $rideData) {
            $result += $rideData->getResult();
        }

        $result = $result / count($rideDataList);
        
        return $result;
    }
}
