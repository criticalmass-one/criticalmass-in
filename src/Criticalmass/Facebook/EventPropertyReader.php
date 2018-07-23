<?php declare(strict_types=1);

namespace App\Criticalmass\Facebook;

use App\Entity\Ride;
use App\Criticalmass\Facebook\Bridge\RideBridge;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class EventPropertyReader
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var RideBridge $rideBridge */
    protected $rideBridge;

    /** @var array $readCities */
    protected $propertyList = [];

    public function __construct(Doctrine $doctrine, RideBridge $rideBridge)
    {
        $this->doctrine = $doctrine;
        $this->rideBridge = $rideBridge;
    }

    public function read(\DateTime $startDateTime = null, \DateTime $endDateTime = null): EventPropertyReader
    {
        $rides = $this->doctrine->getRepository(Ride::class)->findRidesWithFacebookInInterval($startDateTime,
            $endDateTime);

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $property = $this->rideBridge->getEventPropertiesForRide($ride);

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
