<?php
/**
 * Created by PhpStorm.
 * User: maltehuebner
 * Date: 14.09.14
 * Time: 02:47
 */

namespace Caldera\CriticalmassStatisticBundle\Utility\RideGuesser;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RideGuesser {
    protected $controller;
    protected $dateTime;
    protected $latitude;
    protected $longitude;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function setDateTime(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function setCoordinate($latitude, $longitude)
    {

    }

    public function guess()
    {

    }
} 