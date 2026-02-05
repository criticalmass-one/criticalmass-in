<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Deletion;

use App\Entity\User;
use App\Repository\PhotoRepository;
use Doctrine\Persistence\ManagerRegistry;

class PhotoUserPartDeleter implements UserPartDeleterInterface
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly PhotoRepository $photoRepository
    ) {
    }

    public function delete(User $user): void
    {
        $photos = $this->photoRepository->findByUser($user);
        $em = $this->managerRegistry->getManager();

        foreach ($photos as $photo) {
            $em->remove($photo);
        }

        $em->flush();
    }
}
