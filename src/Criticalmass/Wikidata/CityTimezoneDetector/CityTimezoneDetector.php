<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\CityTimezoneDetector;

use App\Entity\City;
use Wikidata\SparqlClient;

class CityTimezoneDetector implements CityTimezoneDetectorInterface
{
    const TIMEZONE_PROPERTY_KEY = 'P2907';

    public function queryForCity(City $city): ?string
    {
        if (!$city->getWikidataEntityId()) {
            return null;
        }

        $query = sprintf('SELECT ?Timezone ?TimezoneLabel ?TimezoneOffset WHERE {
SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }
?Timezone wdt:P31/wdt:P279* wd:Q12143.
wd:%s wdt:P421 ?Timezone.
?Timezone wdt:P2907 ?TimezoneOffset
}
ORDER BY ASC(?TimezoneOffset)', $city->getWikidataEntityId());

        $client = new SparqlClient();

        $timezoneList = $client->execute($query);

        if (is_array($timezoneList) && count($timezoneList) >= 1) {
            $firstTimezone = array_shift($timezoneList);

            if ($timezone = DateTimezoneFactory::createFromOffset((float) $firstTimezone['TimezoneOffset'])) {
                return $timezone->getName();
            }
        }

        return null;
    }
}
