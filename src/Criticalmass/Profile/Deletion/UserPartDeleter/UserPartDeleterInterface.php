<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Deletion\UserPartDeleter;

use App\Entity\User;

interface UserPartDeleterInterface
{
    public function getPriority(): int;
    public function delete(User $user): bool;
}
