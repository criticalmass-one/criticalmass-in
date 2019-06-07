<?php declare(strict_types=1);

namespace App\Criticalmass\CitySlug\Generator;

use App\Entity\City;
use App\Entity\CitySlug;
use Malenki\Slug;

class UmlautsCitySlugGenerator implements CitySlugGeneratorInterface
{
    public function generate(City $city): CitySlug
    {
        $citySlug = new CitySlug();
        $citySlug->setCity($city);

        $cityName = $city->getCity();

        $lowercaseCityName = strtolower($cityName);

        $umlautsLowercaseCityName = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $lowercaseCityName);

        $citySlug->setSlug((string) new Slug($umlautsLowercaseCityName));

        return $citySlug;
    }
}