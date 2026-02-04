<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\Manager;

use App\Entity\Rating;
use App\Entity\Ride;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class RatingManager implements RatingManagerInterface
{
    public function __construct(
        protected readonly ManagerRegistry $registry,
        protected readonly Security $security
    ) {
    }

    public function rateRide(Ride $ride, int $stars): RatingManagerInterface
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User must be logged in to rate a ride');
        }

        $rating = new Rating();
        $rating
            ->setUser($user)
            ->setRide($ride)
            ->setRating($stars);

        $em = $this->registry->getManager();
        $em->persist($rating);
        $em->flush();

        return $this;
    }
}
