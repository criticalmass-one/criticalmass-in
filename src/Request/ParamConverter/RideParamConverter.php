<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\Ride;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class RideParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $ride = $this->findRideById($request);

        if (!$ride) {
            $ride = $this->findRideBySlugs($request);
        }

        if ($ride) {
            $request->attributes->set($configuration->getName(), $ride);
        } else {
            $this->notFound($configuration);
        }
    }

    protected function guessDateTime(string $rideDate): ?\DateTime
    {
        $parts = explode('-', $rideDate);

        if (2 === count($parts)) {
            list($year, $month) = $parts;

            return new \DateTime(sprintf('%s-%s-01', $year, $month));
        }

        if (3 === count($parts)) {
            list($year, $month, $day) = $parts;

            return new \DateTime(sprintf('%s-%s-%s', $year, $month, $day));
        }

        return null;
    }

    protected function findRideById(Request $request): ?Ride
    {
        $rideId = $request->get('rideId');

        if ($rideId) {
            $ride = $this->registry->getRepository(Ride::class)->find($rideId);

            return $ride;
        }

        return null;
    }

    protected function findRideBySlugs(Request $request): ?Ride
    {
        $citySlug = $request->get('citySlug');
        $rideIdentifier = $request->get('rideIdentifier');

        if (!$citySlug || !$rideIdentifier) {
            return null;
        }

        preg_match('/^([0-9]{4,4})\-([0-9]{1,2})(?:\-?)([0-9]{1,2})?$/', $rideIdentifier, $matches);

        if ($citySlug && count($matches) === 0) {
            $city = $this->findCityBySlug($request);

            if ($city) {
                return $this->registry->getRepository(Ride::class)->findOneByCityAndSlug($city, $rideIdentifier);
            }
        } elseif ($citySlug && count($matches) === 3) {
            $rideDateTime = $this->guessDateTime($rideIdentifier);
            $city = $this->findCityBySlug($request);

            if ($city && $rideDateTime) {
                $rides = $this->registry->getRepository(Ride::class)->findByCityAndMonth($city, $rideDateTime);

                return array_shift($rides);
            }
        } elseif ($citySlug && count($matches) === 4) {
            $rideDateTime = $this->guessDateTime($rideIdentifier);
            $city = $this->findCityBySlug($request);

            if ($city && $rideDateTime) {
                return $this->registry->getRepository(Ride::class)->findCityRideByDate($city, $rideDateTime);
            }
        }

        return null;
    }
}
