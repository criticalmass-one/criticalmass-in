<?php declare(strict_types=1);

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\Ride;

class RideTitleTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('ride_title', [$this, 'rideTitle',], ['is_safe' => ['raw']]),
        ];
    }

    public function rideTitle(Ride $ride): string
    {
        if ($ride->getTitle()) {
            return $ride->getTitle();
        }

        $title = sprintf('%s %s', $ride->getCity()->getTitle(), $ride->getDateTime()->format('d.m.Y'));

        return $title;
    }

    public function getName(): string
    {
        return 'ride_title_extension';
    }
}

