<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\Track;

class MapTwigExtension extends \Twig_Extension
{
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('mapUrl', [$this, 'mapUrl'], [
                'is_safe' => ['raw']
            ])
        ];
    }

    public function mapUrl(Track $track): string
    {
        $center = sprintf('%f,%f', $track->getRide()->getLatitude(), $track->getRide()->getLongitude());
        $polylines = sprintf(
            '%s,%d,%d,%d',
            base64_encode($track->getPreviewPolyline()),
            $track->getUser()->getColorRed(),
            $track->getUser()->getColorGreen(),
            $track->getUser()->getColorBlue())
        ;

        $host = 'https://maps.caldera.cc';
        $map = 'staticmap.php';
        $arguments = [
            'maptype' => 'wikimedia-intl',
            'center' => $center,
            'zoom' => 14,
            'size' => '350x200',
            'polylines' => $polylines,
        ];

        return sprintf('%s/%s?%s', $host, $map, http_build_query($arguments));
    }

    public function getName(): string
    {
        return 'track_extension';
    }
}