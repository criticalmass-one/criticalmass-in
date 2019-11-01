<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\TwigExtension;

use App\Criticalmass\Rating\Calculator\RatingCalculatorInterface;
use App\Criticalmass\Rating\StarGenerator\StarGeneratorInterface;
use App\Entity\Ride;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RatingTwigExtension extends AbstractExtension
{
    /** @var RatingCalculatorInterface $ratingCalculator */
    protected $ratingCalculator;

    /** @var StarGeneratorInterface $starGenerator */
    protected $starGenerator;

    public function __construct(RatingCalculatorInterface $ratingCalculator, StarGeneratorInterface $starGenerator)
    {
        $this->ratingCalculator = $ratingCalculator;
        $this->starGenerator = $starGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ride_rating', [$this, 'rideRating'], ['is_safe' => ['html']]),
            new TwigFunction('ride_rating_stars', [$this, 'rideRatingStars'], ['is_safe' => ['html']]),
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

    public function getName(): string
    {
        return 'rating_extension';
    }
}

