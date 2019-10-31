<?php declare(strict_types=1);

namespace App\Criticalmass\Rating\Manager;

use App\Entity\Rating;
use App\Entity\Ride;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RatingManager implements RatingManagerInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    public function __construct(RegistryInterface $registry, TokenStorageInterface $tokenStorage)
    {
        $this->registry = $registry;
        $this->tokenStorage = $tokenStorage;
    }

    public function rateRide(Ride $ride, int $stars): RatingManagerInterface
    {
        $rating = new Rating();
        $rating
            ->setUser($this->tokenStorage->getToken()->getUser())
            ->setRide($ride)
            ->setRating($stars);

        $em = $this->registry->getManager();
        $em->persist($rating);
        $em->flush();

        return $this;
    }
}
