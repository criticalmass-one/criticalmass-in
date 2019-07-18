<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\CityTimezoneDetector;

use App\Entity\City;
use Wikidata\Entity;
use Wikidata\Property;
use Wikidata\Wikidata;

class CityTimezoneDetector implements CityTimezoneDetectorInterface
{
    const TIMEZONE_PROPERTY_KEY = 'P2907';

    public function queryForCity(City $city): ?string
    {
        if (!$city->getWikidataEntityId()) {
            return null;
        }

        $wikidata = new Wikidata();

        /** @var Entity $cityData */
        $cityData = $wikidata->get($city->getWikidataEntityId());
        
        if (!$cityData || !$cityData->properties->has(self::TIMEZONE_PROPERTY_KEY)) {
            return null;
        }

        /** @var Property $timezoneProperty */
        $timezoneProperty = $cityData->properties[self::TIMEZONE_PROPERTY_KEY];

        dump($timezoneProperty);die;
        return DateTimezoneFactory::createFromWikidataProperty($timezoneProperty)->getName();
    }
}
