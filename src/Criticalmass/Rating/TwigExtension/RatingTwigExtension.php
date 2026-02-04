<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\TwigExtension;

use App\Criticalmass\Rating\Calculator\RatingCalculatorInterface;
use App\Criticalmass\Rating\StarGenerator\StarGeneratorInterface;
use App\Entity\Ride;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RatingTwigExtension extends AbstractExtension
{
    public function __construct(
        protected readonly RatingCalculatorInterface $ratingCalculator,
        protected readonly StarGeneratorInterface $starGenerator
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ride_rating', $this->rideRating(...), ['is_safe' => ['html']]),
            new TwigFunction('ride_rating_stars', $this->rideRatingStars(...), ['is_safe' => ['html']]),
        ];
    }

    public function rideRating(Ride $ride): ?float
    {
        return $this->ratingCalculator->calculateRide($ride);
    }

    public function rideRatingStars(Ride $ride): string
    {
        return $this->starGenerator->generateForRide($ride);
    }
}
