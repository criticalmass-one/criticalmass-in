<?php declare(strict_types=1);

namespace App\Criticalmass\Geocoding;

use App\Criticalmass\Geocoding\LocationBuilder\LocationBuilderInterface;
use Geocoder\Geocoder;
use Geocoder\Provider\Provider;
use Geocoder\Query\ReverseQuery;

class ReverseGeocoder implements ReverseGeocoderInterface
{
    const LOCALE = 'de';
    const NOMINATIM_URL = 'https://nominatim.openstreetmap.org';
    const USER_AGENT = 'Critical Mass Photo Geocoder';
    const REFERER = 'https://criticalmass.in/';

    protected $httpClient;

    /** @var Provider $provider */
    protected $provider;

    /** @var Geocoder $geocoder */
    protected $geocoder;

    /** @var LocationBuilderInterface $locationBuilder */
    protected $locationBuilder;

    public function __construct(LocationBuilderInterface $locationBuilder)
    {
        $this->httpClient = new \Http\Adapter\Guzzle6\Client();
        $this->provider = new \Geocoder\Provider\Nominatim\Nominatim($this->httpClient, self::NOMINATIM_URL, self::USER_AGENT, self::REFERER);
        $this->geocoder = new \Geocoder\StatefulGeocoder($this->provider, self::LOCALE);

        $this->locationBuilder = $locationBuilder;
    }

    public function reverseGeocode(ReverseGeocodeable $geocodeable): ReverseGeocodeable
    {
        if (!$geocodeable->getLatitude() || !$geocodeable->getLongitude()) {
            return $geocodeable;
        }

        $result = $this->geocoder->reverseQuery(ReverseQuery::fromCoordinates($geocodeable->getLatitude(), $geocodeable->getLongitude()));

        $firstResult = $result->first();

        if ($firstResult) {
            $geocodeable = $this->locationBuilder->build($geocodeable, $firstResult);
        }

        return $geocodeable;
    }
}
