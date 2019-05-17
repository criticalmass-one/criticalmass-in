<?php declare(strict_types=1);

namespace App\Criticalmass\Timezone\CityTimezoneDetector;

use App\Entity\City;
use Curl\Curl;

class CityTimezoneDetector implements CityTimezoneDetectorInterface
{
    const HOSTNAME = 'http://api.timezonedb.com/v2/get-time-zone';

    /** @var string $timezoneDbApiKey */
    protected $timezoneDbApiKey;

    public function __construct(string $timezoneDbApiKey)
    {
        $this->timezoneDbApiKey = $timezoneDbApiKey;
    }

    public function queryForCity(City $city): ?string
    {
        $query = sprintf('%s?%s', self::HOSTNAME, $this->buildQueryString($city));

        $curl = new Curl();

        $curl->get($query);

        if (200 === $curl->httpStatusCode && $timezone = $curl->response->zoneName) {
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
