<?php declare(strict_types=1);

namespace App\Criticalmass\Geocoding;

use App\Criticalmass\Geocoding\LocationBuilder\LocationBuilderInterface;
use Geocoder\Location;
use Geocoder\Query\ReverseQuery;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\CacheItem;

class CachedReverseGeocoder extends ReverseGeocoder
{
    /** @var AbstractAdapter $cache */
    protected $cache = null;

    public function __construct(LocationBuilderInterface $locationBuilder)
    {
        $redisConnection = RedisAdapter::createConnection('redis://localhost');

        $this->cache = new RedisAdapter(
            $redisConnection,
            $namespace = '',
            $defaultLifetime = 0
        );

        parent::__construct($locationBuilder);
    }

    public function query(ReverseGeocodeable $geocodeable): ?Location
    {
        if (!$geocodeable->getLatitude() || !$geocodeable->getLongitude()) {
            return null;
        }

        try {
            $result = $this->geocoder->reverseQuery(ReverseQuery::fromCoordinates($geocodeable->getLatitude(),
                $geocodeable->getLongitude()));
        } catch (\Exception $exception) {
            return null;
        }

        $firstResult = $result->first();

        return $firstResult;
    }

    public function reverseGeocode(ReverseGeocodeable $geocodeable): ReverseGeocodeable
    {
        $resultLocation = $this->query($geocodeable);

        if ($resultLocation) {
            $geocodeable = $this->locationBuilder->build($geocodeable, $resultLocation);
        }

        return $geocodeable;
    }

    public function getFromCache(ReverseGeocodeable $geocodeable): ?Location
    {
        $nominatimCache = $this->getCacheItem($geocodeable);

        if ($nominatimCache->isHit()) {
            return $nominatimCache->get();
        }

        return null;
    }

    public function saveToCache(ReverseGeocodeable $geocodeable, Location $location): void
    {
        $nominatimCache = $this->getCacheItem($geocodeable);

        $nominatimCache->set($location);
    }

    protected function getCacheItem(ReverseGeocodeable $geocodeable): CacheItem
    {
        $cacheIdentifierSpec = 'criticalmass-nominatim_cache_%.6f_%.6f';
        $cacheIdentifier = sprintf($cacheIdentifierSpec, $geocodeable->getLatitude(), $geocodeable->getLongitude());

        return $this->cache->getItem($cacheIdentifier);
    }
}
