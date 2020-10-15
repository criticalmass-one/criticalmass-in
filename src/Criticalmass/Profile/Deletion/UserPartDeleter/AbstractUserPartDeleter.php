<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Deletion\UserPartDeleter;

abstract class AbstractUserPartDeleter implements UserPartDeleterInterface
{
    public function getPriority(): int
    {
        return 0;
    }
}
