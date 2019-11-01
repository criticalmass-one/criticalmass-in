<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\TwigExtension;

use App\Criticalmass\Rating\Calculator\RatingCalculatorInterface;
use App\Entity\Ride;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RatingTwigExtension extends AbstractExtension
{
    /** @var RatingCalculatorInterface $ratingCalculator */
    protected $ratingCalculator;

    public function __construct(RatingCalculatorInterface $ratingCalculator)
    {
        $this->ratingCalculator = $ratingCalculator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ride_rating', [$this, 'rideRating'], ['is_safe' => ['html']]),
        ];
    }

    public function rideRating(Ride $ride): float
    {
        return $this->ratingCalculator->calculateRide($ride);
    }

    public function getName(): string
    {
        return 'rating_extension';
    }
}

