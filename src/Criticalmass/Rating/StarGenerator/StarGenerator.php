<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\StarGenerator;

use App\Criticalmass\Rating\Calculator\RatingCalculatorInterface;
use App\Entity\Ride;

class StarGenerator implements StarGeneratorInterface
{
    private const string HTML_STAR_FULL = '<i class="fas fa-star"></i>';
    private const string HTML_STAR_HALF = '<i class="fad fa-star-half-alt"></i>';
    private const string HTML_STAR_EMPTY = '<i class="far fa-star"></i>';

    public function __construct(
        protected readonly RatingCalculatorInterface $ratingCalculator
    ) {
    }

    public function generateForRide(Ride $ride): string
    {
        $rating = $this->ratingCalculator->calculateRide($ride);

        $stars = [];

        while ($rating && $rating >= 1) {
            $stars[] = self::HTML_STAR_FULL;

            $rating -= 1;
        }

        if ($rating && $rating < 1) {
            $stars[] = self::HTML_STAR_HALF;
        }

        for ($i = count($stars); $i < 5; ++$i) {
            $stars[] = self::HTML_STAR_EMPTY;
        }

        return implode('', $stars);
    }
}
