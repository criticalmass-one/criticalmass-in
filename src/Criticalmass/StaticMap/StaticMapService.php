<?php declare(strict_types=1);

namespace App\Criticalmass\StaticMap;

use App\Criticalmass\StaticMap\Cache\StaticMapCache;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StaticMapService
{
    private HttpClientInterface $httpClient;

    public function __construct(
        private readonly StaticMapCache $cache,
        private readonly string $mapsJetztApiUrl,
    ) {
        $this->httpClient = HttpClient::create();
    }

    public function generatePolylineMap(
        string $polyline,
        string $color,
        int $width = 600,
        int $height = 150,
        int $strokeWidth = 3,
    ): ?string {
        $parameters = [
            'type' => 'polyline',
            'polyline' => $polyline,
            'color' => $this->normalizeColor($color),
            'width' => $width,
            'height' => $height,
            'strokeWidth' => $strokeWidth,
        ];

        $cacheKey = StaticMapCache::generateCacheKey($parameters);

        return $this->cache->get($cacheKey, fn() => $this->fetchMapUrl($parameters));
    }

    public function generateMarkerMap(
        float $latitude,
        float $longitude,
        string $markerType = 'city',
        string $color = '#FF0000',
        int $width = 600,
        int $height = 150,
    ): ?string {
        $icon = $this->mapMarkerTypeToIcon($markerType);

        $parameters = [
            'type' => 'marker',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'icon' => $icon,
            'color' => $this->normalizeColor($color),
            'width' => $width,
            'height' => $height,
        ];

        $cacheKey = StaticMapCache::generateCacheKey($parameters);

        return $this->cache->get($cacheKey, fn() => $this->fetchMapUrl($parameters));
    }

    /** @param array<string, string|int|float> $parameters */
    private function fetchMapUrl(array $parameters): ?string
    {
        try {
            $queryString = http_build_query($parameters);
            $url = sprintf('%s?%s', $this->mapsJetztApiUrl, $queryString);

            $response = $this->httpClient->request('GET', $url);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            return $url;
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeColor(string $color): string
    {
        if (!str_starts_with($color, '#')) {
            return '#' . $color;
        }

        return $color;
    }

    private function mapMarkerTypeToIcon(string $markerType): string
    {
        return match ($markerType) {
            'ride' => 'bicycle',
            'city' => 'location-dot',
            default => 'location-dot',
        };
    }
}
