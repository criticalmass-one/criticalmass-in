<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RideParamConverter extends AbstractParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $ride = null;

        $rideId = $request->get('rideId');

        if ($rideId) {
            $ride = $this->registry->getRepository(Ride::class)->find($rideId);

            if ($ride) {
                $request->attributes->set($configuration->getName(), $ride);

                return;
            }
        }

        $citySlug = $request->get('citySlug');
        $rideDate = $request->get('rideDate');

        if ($citySlug && $rideDate) {
            $city = $this->findCityBySlug($citySlug);
            $rideDateTime = $this->guessDateTime($rideDate);

            if ($city && $rideDateTime) {
                $ride = $this->registry->getRepository(Ride::class)->findCityRideByDate($city, $rideDateTime);
            }

            if ($ride) {
                $request->attributes->set($configuration->getName(), $ride);

                return;
            }
        }

        if (!$ride) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
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
}
