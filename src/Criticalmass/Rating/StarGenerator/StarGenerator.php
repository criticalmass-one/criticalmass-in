<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\StarGenerator;

use App\Criticalmass\Rating\Calculator\RatingCalculatorInterface;
use App\Entity\Ride;

class StarGenerator implements StarGeneratorInterface
{
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
            $stars[] = '<i class="fas fa-star"></i>';

            $rating -= 1;
        }

        if ($rating && $rating < 1) {
            $stars[] = '<i class="fad fa-star-half-alt"></i>';
        }

        for ($i = count($stars); $i < 5; ++$i) {
            $stars[] = '<i class="far fa-star"></i>';
        }

        return join('', $stars);
    }
}
