<?php declare(strict_types=1);

namespace App\Criticalmass\CitySlug\Generator;

use App\Entity\City;
use App\Entity\CitySlug;

class SimpleCitySlugGenerator extends AbstractCitySlugGenerator
{
    public function generate(City $city): CitySlug
    {
        $citySlug = new CitySlug();
        $citySlug->setCity($city);

        $cityName = $city->getCity();

        $lowercaseCityName = mb_strtolower($cityName);

        $citySlug->setSlug($this->createSlug($lowercaseCityName));

        return $citySlug;
    }
}