<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RideParamConverter extends DoctrineParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $ride = null;

        $rideId = $request->get('rideId');

        if ($rideId) {
            $ride = $this->registry->getRepository(Ride::class)->find($rideId);
        }

        $citySlug = $request->get('citySlug');
        $rideDate = $request->get('rideDate');

        if ($citySlug && $rideDate) {
            $ride = $this->registry->getRepository(Ride::class)->findByCitySlugAndRideDate($citySlug, $rideDate);
        }

        if ($ride) {
            $request->attributes->set($configuration->getName(), $ride);
        } else {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
        }
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getClass() === 'AppBundle:Ride';
    }
}
