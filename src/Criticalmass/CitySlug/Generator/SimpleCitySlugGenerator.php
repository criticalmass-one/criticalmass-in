<?php declare(strict_types=1);

namespace App\Criticalmass\CitySlug\Generator;

use App\Entity\City;
use App\Entity\CitySlug;
use Malenki\Slug;

class SimpleCitySlugGenerator implements CitySlugGeneratorInterface
{
    public function generate(City $city): CitySlug
    {
        $citySlug = new CitySlug();
        $citySlug->setCity($city);

        $cityName = $city->getCity();

        $lowercaseCityName = strtolower($cityName);

        $citySlug->setSlug((string) new Slug($lowercaseCityName));

        return $citySlug;
    }
}