<?php

namespace Caldera\CriticalmassCoreBundle\Utility;

use Caldera\CriticalmassCoreBundle\Entity\Ride;
use \Caldera\CriticalmassCoreBundle\Entity\Position;
use \Caldera\CriticalmassCoreBundle\Entity\RideTrack;


class RideCompiler
{
    protected $ride;
    protected $doctrine;

    public function __construct(Ride $ride, $doctrine)
    {
        $this->ride = $ride;
        $this->doctrine = $doctrine;
    }

    public function execute()
    {
        $positions = $this->doctrine->getRepository('CalderaCriticalmassCoreBundle:Position')->findByRide($this->ride->getId());

        $latestPosition = null;

        $dc = new DistanceCalculator();

        foreach ($positions as $position)
        {

            if (!$latestPosition)
            {
                $latestPosition = $position;
                $this->convertPositionToRideTrack($position);
            }
            else
            {
                $distance = $dc->calculateDistanceFromPositionToPosition($latestPosition, $position);

                if ($distance > 0.05)
                {
                    $this->convertPositionToRideTrack($position);
                    $latestPosition = $position;
                }
            }
        }
    }

    protected function convertPositionToRideTrack(Position $position)
    {
        $rt = new RideTrack();
        $rt->setRide($this->ride);
        $rt->setLatitude($position->getLatitude());
        $rt->setLongitude($position->getLongitude());
        $rt->setDateTime($position->getCreationDateTime());

        $manager = $this->doctrine->getManager();
        $manager->persist($rt);
        $manager->flush();
    }
} 