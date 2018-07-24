<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\Track;
use App\EntityInterface\StaticMapableInterface;

class StaticmapsTwigExtension extends \Twig_Extension
{
    /** @var string $staticmapsHost */
    protected $staticmapsHost = '';

    /** @var array $defaultParameters */
    protected $defaultParameters = [
        'size' => '865x512',
    ];

    public function __construct(string $staticmapsHost)
    {
        $this->staticmapsHost = $staticmapsHost;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('static_map', [$this, 'staticmap',], ['is_safe' => ['raw']]),
        ];
    }

    public function staticmap(StaticMapableInterface $object, int $width = null, int $height = null, int $zoom = null): string
    {
        if ($object instanceof Ride) {
            return $this->staticmapsRide($object, $width, $height, $zoom);
        } elseif ($object instanceof City) {
            return $this->staticmapsCity($object, $width, $height, $zoom);
        } elseif ($object instanceof Track) {
            return $this->staticmapsTrack($object, $width, $height, $zoom);
        } elseif ($object instanceof Photo) {
            return $this->staticmapsPhoto($object, $width, $height, $zoom);
        }

        return '';
    }

    public function getName(): string
    {
        return 'staticmaps_extension';
    }

    public function staticmapsTrack(Track $track, int $width = null, int $height = null, int $zoom = null): string
    {
        if (!$track->getReducedPolyline()) {
            return '';
        }

        $parameters = [
            'polylines' => sprintf('%s,%d,%d,%d', base64_encode($track->getReducedPolyline()), $track->getColorRed(), $track->getColorGreen(), $track->getColorBlue()),
        ];

        return $this->generateMapUrl($parameters, $width, $height, $zoom);
    }

    public function staticmapsRide(Ride $ride, int $width = null, int $height = null, int $zoom = null): string
    {
        if ($ride->getHasLocation() && $ride->getLatitude() && $ride->getLongitude()) {
            $latitude = $ride->getLatitude();
            $longitude = $ride->getLongitude();
        } else {
            $latitude = $ride->getCity()->getLatitude();
            $longitude = $ride->getCity()->getLongitude();
        }

        $parameters = [
            'markers' => sprintf('%f,%f,%s,%s,%s', $latitude, $longitude, 'circle', 'red', 'bicycle'),
        ];

        return $this->generateMapUrl($parameters, $width, $height, $zoom);
    }

    public function staticmapsPhoto(Photo $photo, int $width = null, int $height = null, int $zoom = null): string
    {
        $parameters = [
            'markers' => sprintf('%f,%f,%s,%s,%s', $photo->getLatitude(), $photo->getLongitude(), 'square', 'yellow', 'camera'),
        ];

        if ($track = $this->findTrackForPhoto($photo)) {
            if ($track->getReducedPolyline()) {
                $parameters['polylines'] = sprintf('%s,%d,%d,%d', base64_encode($track->getReducedPolyline()), $track->getColorRed(), $track->getColorGreen(), $track->getColorBlue());
            }
        }

        return $this->generateMapUrl($parameters, $width, $height, $zoom);
    }

    protected function findTrackForPhoto(Photo $photo): ?Track
    {
        /** @var Track $track */
        foreach ($photo->getRide()->getTracks() as $track) {
            if ($track->getUser() === $photo->getUser()) {
                return $track;
            }
        }

        return null;
    }

    public function staticmapsCity(City $city, int $width = null, int $height = null, int $zoom = null): string
    {
        $parameters = [
            'markers' => sprintf('%f,%f,%s,%s,%s', $city->getLatitude(), $city->getLongitude(), 'circle', 'blue', 'university'),
        ];

        return $this->generateMapUrl($parameters, $width, $height, $zoom);
    }

    protected function generateMapUrl(array $parameters = [], int $width = null, int $height = null, int $zoom = null): string
    {
        $viewParameters = [];

        if ($width && $height) {
            $viewParameters['size'] = sprintf('%dx%d', $width, $height);
        }

        if ($zoom) {
            $viewParameters['zoom'] = sprintf('%d', $zoom);
        }

        $parameters = array_merge($parameters, $this->defaultParameters, $viewParameters);

        return sprintf('%sstaticmap.php?%s', $this->staticmapsHost, $this->generateMapUrlParameters($parameters));
    }

    protected function generateMapUrlParameters(array $parameters = []): string
    {
        $list = [];

        foreach ($parameters as $key => $value) {
            $list [] = sprintf('%s=%s', $key, $value);
        }

        return implode('&', $list);
    }
}

