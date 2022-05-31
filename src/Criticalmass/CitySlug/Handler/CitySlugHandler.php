<?php declare(strict_types=1);

namespace App\Criticalmass\CitySlug\Handler;

use App\Criticalmass\CitySlug\Generator\SimpleCitySlugGenerator;
use App\Criticalmass\CitySlug\Generator\UmlautsCitySlugGenerator;
use App\Entity\City;

class CitySlugHandler
{
    public static function createSlugsForCity(City $city): array
    {
        $citySlugs = [];

        $citySlugs[] = (new SimpleCitySlugGenerator())->generate($city);

        $citySlugs[] = (new UmlautsCitySlugGenerator())->generate($city);

        $citySlugs = array_unique($citySlugs); // kicks out umlaut slug if there are no umlauts

        foreach ($citySlugs as $citySlug) {
            $city->addSlug($citySlug);
        }

        return $citySlugs;
    }
}