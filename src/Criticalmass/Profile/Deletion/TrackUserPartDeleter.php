<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Deletion;

use App\Entity\User;
use App\Repository\TrackRepository;
use Doctrine\Persistence\ManagerRegistry;

class TrackUserPartDeleter implements UserPartDeleterInterface
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly TrackRepository $trackRepository
    ) {
    }

    public function delete(User $user): void
    {
        $tracks = $this->trackRepository->findByUser($user);
        $em = $this->managerRegistry->getManager();

        foreach ($tracks as $track) {
            $em->remove($track);
        }

        $em->flush();
    }
}
