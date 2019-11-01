<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\Calculator;

use App\Entity\Rating;
use App\Entity\Ride;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RatingCalculator implements RatingCalculatorInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function calculateRide(Ride $ride): ?float
    {
        $ratings = $this->registry->getRepository(Rating::class)->findByRide($ride);

        if (count($ratings) === 0) {
            return null;
        }

        $ratingSum = 0;

        /** @var Rating $rating */
        foreach ($ratings as $rating) {
            $ratingSum += $rating->getRating();
        }

        return round($ratingSum / count($ratings), 2);
    }
}
