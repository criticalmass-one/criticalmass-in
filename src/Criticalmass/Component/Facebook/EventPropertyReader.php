<?php declare(strict_types=1);

namespace Criticalmass\Component\Facebook;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\Facebook\Api\FacebookEventRideApi;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class EventPropertyReader
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var FacebookEventRideApi $facebookEventRideApi */
    protected $facebookEventApi;

    /** @var array $readCities */
    protected $propertyList = [];

    public function __construct(Doctrine $doctrine, FacebookEventRideApi $facebookEventRideApi)
    {
        $this->doctrine = $doctrine;
        $this->facebookEventRideApi = $facebookEventRideApi;
    }

    public function read(): EventPropertyReader
    {
        $rides = $this->doctrine->getRepository(Ride::class)->findRidesWithFacebookInInterval();

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $property = $this->facebookEventRideApi->getEventPropertiesForRide($ride);

            if ($property) {
                $this->doctrine->getManager()->persist($property);

                $this->propertyList[] = $property;
            }
        }

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function getPropertyList(): array
    {
        return $this->propertyList;
    }
}
