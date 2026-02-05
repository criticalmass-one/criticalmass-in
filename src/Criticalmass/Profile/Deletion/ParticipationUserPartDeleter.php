<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Deletion;

use App\Entity\User;
use App\Repository\ParticipationRepository;
use Doctrine\Persistence\ManagerRegistry;

class ParticipationUserPartDeleter implements UserPartDeleterInterface
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly ParticipationRepository $participationRepository
    ) {
    }

    public function delete(User $user): void
    {
        $participations = $this->participationRepository->findByUser($user);
        $em = $this->managerRegistry->getManager();

        foreach ($participations as $participation) {
            $em->remove($participation);
        }

        $em->flush();
    }
}
