<?php

namespace Criticalmass\Bundle\AppBundle\Twig\Extension;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;

class StaticmapsTwigExtension extends \Twig_Extension
{
    /** @var string $staticmapsHost */
    protected $staticmapsHost = '';

    /** @var array $defaultParameters */
    protected $defaultParameters = [
        'maptype' => 'wikimedia-intl',
        'zoom' => 14,
        'size' => '865x512',
    ];

    public function __construct(string $staticmapsHost)
    {
        $this->staticmapsHost = $staticmapsHost;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('staticmaps', [$this, 'staticmaps',], ['is_safe' => ['raw']]),
        ];
    }

    public function staticmaps($object, int $width = null, int $height = null, int $zoom = null): string
    {
        if ($object instanceof Ride) {
            return $this->staticmapsRide($object, $width, $height, $zoom);
        } elseif ($object instanceof City) {
            return $this->staticmapsCity($object, $width, $height, $zoom);
        }

        return '';
    }

    public function getName(): string
    {
        return 'staticmaps_extension';
    }

    public function staticmapsRide(Ride $ride, int $width = null, int $height = null, int $zoom = null): string
    {
        $parameters = [
            'center' => sprintf('%f,%f', $ride->getLatitude(), $ride->getLongitude()),
            'markers' => sprintf('%f,%f,%s,%s,%s', $ride->getLatitude(), $ride->getLongitude(), 'circle', 'red', 'bicycle'),
        ];

        return $this->generateMapUrl($parameters, $width, $height, $zoom);
    }

    public function staticmapsCity(City $city, int $width = null, int $height = null, int $zoom = null): string
    {
        $parameters = [
            'center' => sprintf('%f,%f', $city->getLatitude(), $city->getLongitude()),
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

        return implode('&',$list);
    }
}

