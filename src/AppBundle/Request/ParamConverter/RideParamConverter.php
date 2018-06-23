<?php

namespace AppBundle\Request\ParamConverter;

use AppBundle\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class RideParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(Registry $registry)
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
        $rideDate = $request->get('rideDate');

        if ($citySlug && $rideDate) {
            $city = $this->findCityBySlug($request);
            $rideDateTime = $this->guessDateTime($rideDate);

            if ($city && $rideDateTime) {
                return $this->registry->getRepository(Ride::class)->findCityRideByDate($city, $rideDateTime);
            }
        }

        return null;
    }
}
