<?php
/**
 * Created by PhpStorm.
 * User: maltehuebner
 * Date: 14.09.14
 * Time: 02:47
 */

namespace Caldera\CriticalmassStatisticBundle\Utility\RideGuesser;

use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RideGuesser {
    protected $controller;
    protected $gpx;
    protected $rides = array();

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function setGpx($gpx)
    {
        $this->gpx = $gpx;
    }

    public function guess()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);
        
        $dateTime = $gr->getStartDateTime();

        $this->rides = $this->controller->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findRidesByLatitudeLongitudeDateTime($gr->getLatitudeOfPoint(0), $gr->getLongitudeOfPoint(0), $dateTime);
    }

    public function getRides()
    {
        return $this->rides;
    }

    public function isDistinct()
    {
        return count($this->rides) == 1;
    }

    public function isImpossible()
    {
        return count($this->rides) == 0;
    }
    
    public function isDuplicate()
    {
        if ($this->isDistinct())
        {
            $ride = $this->rides[0];
            $user = $this->controller->getUser();
            
            $duplicate = $this->controller->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Track')->findBy(
                array('ride' => $ride, 'user' => $user)
            );
            
            return $duplicate != null;
        }
        
        throw new \Exception('Please call this method only on distinct rides.');
    }
} 