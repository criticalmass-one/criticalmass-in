<?php

namespace AppBundle\Model;

use AppBundle\Entity\City;
use AppBundle\Entity\Ride;

class CreateEstimateModel
{
    /** @var \DateTime $dateTime */
    protected $dateTime;

    /** @var float $latitude */
    protected $latitude;

    /** @var float $longitude */
    protected $longitude;

    /** @var int $estimation */
    protected $estimation;

    public function __construct(\DateTime $dateTime = null, float $latitude = null, float $longitude = null, int $estimation = null)
    {
        $this->dateTime = $dateTime;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->estimation = $estimation;
    }

    public function setDateTime(\DateTime $dateTime): CreateEstimateModel
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setLatitude(float $latitude): CreateEstimateModel
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude): CreateEstimateModel
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setEstimation(int $estimation): CreateEstimateModel
    {
        $this->estimation = $estimation;

        return $this;
    }

    public function getEstimation(): float
    {
        return $this->estimation;
    }
}
