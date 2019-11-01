<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\StarGenerator;

use App\Criticalmass\Rating\Calculator\RatingCalculatorInterface;
use App\Entity\Ride;

class StarGenerator implements StarGeneratorInterface
{
    const HTML_STAR_FULL = '<i class="fas fa-star"></i>';
    const HTML_STAR_HALF = '<i class="fad fa-star-half-alt"></i>';
    const HTML_STAR_EMPTY = '<i class="far fa-star"></i>';

    /** @var RatingCalculatorInterface $ratingCalculator */
    protected $ratingCalculator;

    public function __construct(RatingCalculatorInterface $ratingCalculator)
    {
        $this->ratingCalculator = $ratingCalculator;
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

        return join('', $stars);
    }
}
