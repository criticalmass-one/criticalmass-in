<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Deletion;

use App\Criticalmass\Profile\Deletion\UserPartDeleter\UserPartDeleterInterface;
use App\Entity\User;

interface UserDeleterInterface
{
    public function addPartDeleter(UserPartDeleterInterface $userPartDeleter): self;
    public function deleteUser(User $user): void;
}
