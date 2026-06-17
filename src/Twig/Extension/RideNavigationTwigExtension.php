<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Ride;
use App\Repository\RideRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Stadt-gescopte Vor-/Zurück-Navigation zwischen Rides (#1353). Ersetzt die
 * generischen ordered-entities-Funktionen, die die City nicht zuverlässig
 * scopen und stadtübergreifend navigieren.
 */
class RideNavigationTwigExtension extends AbstractExtension
{
    public function __construct(private readonly RideRepository $rideRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('previous_ride', [$this, 'previousRide']),
            new TwigFunction('next_ride', [$this, 'nextRide']),
        ];
    }

    public function previousRide(Ride $ride): ?Ride
    {
        return $this->rideRepository->getPreviousRide($ride);
    }

    public function nextRide(Ride $ride): ?Ride
    {
        return $this->rideRepository->getNextRide($ride);
    }

    public function getName(): string
    {
        return 'ride_navigation_extension';
    }
}
