<?php

namespace Caldera\CriticalmassCoreBundle\Utility\CitySlugGenerator;

use Caldera\CriticalmassCoreBundle\Entity\City;
use Caldera\CriticalmassCoreBundle\Entity\CitySlug;

class CitySlugGenerator {
    protected $city = null;

    public function __construct(City $city)
    {
        $this->city = $city;
    }

    public function execute()
    {
        $slug = strtolower($this->city->getCity());

        $slug = str_replace(array('ä', 'ö', 'ü', 'ß'), array('ae', 'oe', 'ue', 'ss'), $slug);

        $citySlug = new CitySlug();
        $citySlug->setCity($this->city);
        $citySlug->setSlug($slug);

        return $citySlug;
    }
} 