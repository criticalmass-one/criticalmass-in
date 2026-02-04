<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\Calculator;

use App\Entity\Rating;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;

class RatingCalculator implements RatingCalculatorInterface
{
    public function __construct(
        protected readonly ManagerRegistry $registry
    ) {
    }

    public function calculateRide(Ride $ride): ?float
    {
        $ratings = $this->registry->getRepository(Rating::class)->findBy(['ride' => $ride]);

        if (count($ratings) === 0) {
            return null;
        }

        $ratingSum = 0;

        foreach ($ratings as $rating) {
            $ratingSum += $rating->getRating();
        }

        return round($ratingSum / count($ratings), 2);
    }
}
