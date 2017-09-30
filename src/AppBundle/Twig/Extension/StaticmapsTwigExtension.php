<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\City;
use AppBundle\Entity\Ride;

class StaticmapsTwigExtension extends \Twig_Extension
{
    /** @var string $staticmapsHost */
    protected $staticmapsHost = '';

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

    public function staticmaps($object): string
    {
        if ($object instanceof Ride) {
            return $this->staticmapsRide($object);
        } elseif ($object instanceof City) {
            return $this->staticmapsCity($object);
        }

        return '';
    }

    public function getName(): string
    {
        return 'staticmaps_extension';
    }

    public function staticmapsRide(Ride $ride): string
    {
        $parameters = [
            sprintf('center=%f,%f', $ride->getLatitude(), $ride->getLongitude()),
            sprintf('markers=%f,%f,%s,%s', $ride->getLatitude(), $ride->getLongitude(), 'red', 'bicycle'),
            'zoom=14',
            'size=865x512',
            'maptype=wikimedia-intl',
        ];

        return $this->generateMapUrl($parameters);
    }

    public function staticmapsCity(City $city): string
    {
        $parameters = [
            sprintf('center=%f,%f', $city->getLatitude(), $city->getLongitude()),
            sprintf('markers=%f,%f,%s,%s', $city->getLatitude(), $city->getLongitude(), 'blue', 'university'),
            'zoom=14',
            'size=865x512',
            'maptype=wikimedia-intl',
        ];

        return $this->generateMapUrl($parameters);
    }

    protected function generateMapUrl(array $parameters = []): string
    {
        return sprintf('%sstaticmap.php?%s', $this->staticmapsHost, implode('&',$parameters));
    }
}

