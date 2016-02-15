<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\CitySlugGenerator;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\CitySlug;

class CitySlugGenerator {
    protected $city = null;

    public function __construct(City $city)
    {
        $this->city = $city;
    }

    public function execute()
    {
        $slug = strtolower($this->city->getCity());

        $slug = str_replace
        (
            [
                'ä',
                'ö',
                'ü',
                'ß'
            ],
            [
                'ae',
                'oe',
                'ue',
                'ss'
            ],
            $slug
        );

        $citySlug = new CitySlug();
        $citySlug->setCity($this->city);
        $citySlug->setSlug($slug);

        return $citySlug;
    }
} 