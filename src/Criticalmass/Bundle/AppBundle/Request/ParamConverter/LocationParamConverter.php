<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Criticalmass\Bundle\AppBundle\Entity\Location;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationParamConverter extends DoctrineParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $location = null;

        $locationSlug = $request->get('locationSlug');

        if ($locationSlug) {
            $location = $this->registry->getRepository(Location::class)->findOneBySlug($locationSlug);
        }

        if ($location) {
            $request->attributes->set($configuration->getName(), $location);
        } else {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
        }
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getClass() === 'AppBundle:Location';
    }
}
