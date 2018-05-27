<?php declare(strict_types=1);

namespace Criticalmass\Component\Cycles\Analyzer;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CityCycle;
use Criticalmass\Component\RideGenerator\RideGenerator\RideGenerator;
use Doctrine\Bundle\DoctrineBundle\Registry;

class CycleAnalyzer implements CycleAnalyzerInterface
{
    /** @var City $city */
    protected $city;

    /** @var array $cycles */
    protected $cycles = [];

    /** @var Registry $registry */
    protected $registry;

    /** @var RideGenerator $rideGenerator */
    protected $rideGenerator;

    public function __construct(Registry $registry, RideGenerator $rideGenerator)
    {
        $this->registry = $registry;

        $this->rideGenerator = $rideGenerator;
    }

    public function setCity(City $city): CycleAnalyzerInterface
    {
        $this->city = $city;

        $this->rideGenerator->setCityList([$city]);

        return $this;
    }

    public function analyze(): CycleAnalyzerInterface
    {
        return $this;
    }

    protected function fetchCycles(): CycleAnalyzer
    {
        return $this->registry->getRepository(CityCycle::class)->findByCity($this->city);
    }

}
