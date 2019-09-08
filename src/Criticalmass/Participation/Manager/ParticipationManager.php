<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\Manager;

use App\Entity\Participation;
use App\Entity\Ride;
use App\Entity\User;
use App\Event\Participation\ParticipationCreatedEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ParticipationManager implements ParticipationManagerInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    /** @var EventDispatcherInterface $eventDispatcher */
    protected $eventDispatcher;

    public function __construct(RegistryInterface $registry, TokenStorageInterface $tokenStorage, EventDispatcherInterface $eventDispatcher)
    {
        $this->registry = $registry;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function participate(Ride $ride, string $status): Participation
    {
        $participation = $this->findOrCreateParticipation($ride);

        $participation
            ->setGoingYes($status === 'yes')
            ->setGoingMaybe($status === 'maybe')
            ->setGoingNo($status === 'no');

        $em = $this->registry->getManager();
        $em->persist($participation);
        $em->flush();

        $this->eventDispatcher->dispatch(ParticipationCreatedEvent::NAME, new ParticipationCreatedEvent($participation));

        return $participation;
    }

    protected function findOrCreateParticipation(Ride $ride): Participation
    {
        $participation = $this->registry->getRepository(Participation::class)->findParticipationForUserAndRide($this->getUser(), $ride);

        if (!$participation) {
            $participation = new Participation();
            $participation
                ->setRide($ride)
                ->setUser($this->getUser());
        }

        return $participation;
    }

    protected function getUser(): User
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
