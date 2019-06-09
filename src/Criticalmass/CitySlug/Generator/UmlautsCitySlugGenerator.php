<?php declare(strict_types=1);

namespace App\Criticalmass\CitySlug\Generator;

use App\Entity\City;
use App\Entity\CitySlug;

class UmlautsCitySlugGenerator extends AbstractCitySlugGenerator
{
    public function generate(City $city): CitySlug
    {
        $citySlug = new CitySlug();
        $citySlug->setCity($city);

        $cityName = $city->getCity();

        $lowercaseCityName = mb_strtolower($cityName);

        $umlautsLowercaseCityName = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $lowercaseCityName);

        $citySlug->setSlug($this->createSlug($umlautsLowercaseCityName));

        return $citySlug;
    }
}