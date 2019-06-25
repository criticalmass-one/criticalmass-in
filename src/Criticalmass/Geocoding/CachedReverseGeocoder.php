<?php declare(strict_types=1);

namespace App\Criticalmass\Geocoding;

use App\Criticalmass\Geocoding\LocationBuilder\LocationBuilderInterface;
use Geocoder\Location;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\CacheItem;

class CachedReverseGeocoder extends ReverseGeocoder
{
    /** @var AbstractAdapter $cache */
    protected $cache = null;

    /** @var int */
    protected const LIFETIME = 2678400; // 60 * 60 * 24 * 31 = 2678400

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
        if ($resultLocation = $this->getFromCache($geocodeable)) {
            return $resultLocation;
        }

        $resultLocation = parent::query($geocodeable);

        if ($resultLocation) {
            $this->saveToCache($geocodeable, $resultLocation);
        }

        return $resultLocation;
    }

    protected function getFromCache(ReverseGeocodeable $geocodeable): ?Location
    {
        $nominatimCache = $this->getCacheItem($geocodeable);

        if ($nominatimCache->isHit()) {
            return $nominatimCache->get();
        }

        return null;
    }

    protected function saveToCache(ReverseGeocodeable $geocodeable, Location $location): void
    {
        $nominatimCache = $this->getCacheItem($geocodeable);

        $nominatimCache
            ->set($location)
            ->expiresAfter(self::LIFETIME);

        $this->cache->save($nominatimCache);
    }

    protected function getCacheItem(ReverseGeocodeable $geocodeable): CacheItem
    {
        $cacheIdentifierSpec = 'criticalmass-nominatim_cache_%.6f_%.6f';
        $cacheIdentifier = sprintf($cacheIdentifierSpec, $geocodeable->getLatitude(), $geocodeable->getLongitude());

        return $this->cache->getItem($cacheIdentifier);
    }
}
