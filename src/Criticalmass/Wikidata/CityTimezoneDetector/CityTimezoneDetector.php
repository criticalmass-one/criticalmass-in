<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\CityTimezoneDetector;

use App\Entity\City;
use Curl\Curl;

class CityTimezoneDetector implements CityTimezoneDetectorInterface
{
    const HOSTNAME = 'http://api.timezonedb.com/v2/get-time-zone';

    /** @var string $timezoneDbApiKey */
    protected $timezoneDbApiKey;

    /** @var Curl $curl */
    protected $curl;

    public function __construct(string $timezoneDbApiKey)
    {
        $this->timezoneDbApiKey = $timezoneDbApiKey;
        $this->curl = new Curl();
    }

    public function queryForCity(City $city): ?string
    {
        if (!$city->getLatitude() || !$city->getLongitude()) {
            return null;
        }

        $query = sprintf('%s?%s', self::HOSTNAME, $this->buildQueryString($city));

        $this->curl->get($query);

        if (200 === $this->curl->httpStatusCode && $timezone = $this->curl->response->zoneName) {
            return $timezone;
        }

        return null;
    }

    protected function buildQueryString(City $city): string
    {
        $parameterList = [
            'key' => $this->timezoneDbApiKey,
            'by' => 'position',
            'format' => 'json',
            'lat' => $city->getLatitude(),
            'lng' => $city->getLongitude(),
        ];

        return http_build_query($parameterList);
    }
}
