<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Deletion;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class UserDeleter
{
    /** @var iterable<UserPartDeleterInterface> */
    private iterable $userPartDeleterList;

    /**
     * @param iterable<UserPartDeleterInterface> $userPartDeleterList
     */
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        iterable $userPartDeleterList = []
    ) {
        $this->userPartDeleterList = $userPartDeleterList;
    }

    public function delete(User $user): void
    {
        foreach ($this->userPartDeleterList as $userPartDeleter) {
            $userPartDeleter->delete($user);
        }

        $em = $this->managerRegistry->getManager();
        $em->remove($user);
        $em->flush();
    }
}
