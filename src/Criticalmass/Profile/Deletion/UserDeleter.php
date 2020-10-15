<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Deletion;

use App\Criticalmass\Profile\Deletion\UserPartDeleter\UserPartDeleterInterface;
use App\Entity\User;

class UserDeleter implements UserDeleterInterface
{
    protected array $partDeleterList = [];

    public function __construct()
    {

    }

    public function addPartDeleter(UserPartDeleterInterface $userPartDeleter): self
    {
        $this->partDeleterList[] = $userPartDeleter;

        return $this;
    }

    protected function sortPartDeleterByPriority(): self
    {
        usort($this->partDeleterList, function (UserPartDeleterInterface $a, UserPartDeleterInterface $b): int
        {
            if ($a->getPriority() === $b->getPriority()) {
                return 0;

            }

            return ($a->getPriority() < $b->getPriority() ? -1 : 1);
        });

        return $this;
    }

    public function deleteUser(User $user): void
    {
        /** @var UserPartDeleterInterface $partDeleter */
        foreach ($this->partDeleterList as $partDeleter) {
            $partDeleter->delete($user);
        }
    }
}
