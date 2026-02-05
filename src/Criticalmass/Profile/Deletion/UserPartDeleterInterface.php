<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Deletion;

use App\Entity\User;

interface UserPartDeleterInterface
{
    public function delete(User $user): void;
}
