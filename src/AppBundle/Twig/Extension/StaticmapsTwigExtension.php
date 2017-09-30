<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\Ride;

class StaticmapsTwigExtension extends \Twig_Extension
{
    protected $staticmapsHost = '';

    public function __construct(string $staticmapsHost)
    {
        $this->staticmapsHost = $staticmapsHost;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('staticmaps', [$this, 'staticmaps',], ['is_safe' => ['raw']]),
        ];
    }

    public function staticmaps($object): string
    {
        if ($object instanceof Ride) {
            return $this->staticmapsRide($object);
        }

        return '';
    }

    public function getName()
    {
        return 'staticmaps_extension';
    }

    public function staticmapsRide(Ride $ride): string
    {
        $parameters = [
            sprintf('center=%f,%f', $ride->getLatitude(), $ride->getLongitude()),
            sprintf('markers=%f,%f', $ride->getLatitude(), $ride->getLongitude()),
            'zoom=14',
            'size=865x512',
            'maptype=wikimedia-intl',
        ];

        return sprintf('%sstaticmap.php?%s', $this->staticmapsHost, implode('&',$parameters));
    }
}

