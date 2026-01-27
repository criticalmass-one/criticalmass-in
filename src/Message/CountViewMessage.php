<?php declare(strict_types=1);

namespace App\Message;

use Carbon\Carbon;

class CountViewMessage
{
    public function __construct(
        private readonly int $entityId,
        private readonly string $entityClassName,
        private readonly ?int $userId,
        private readonly Carbon $dateTime
    ) {
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getDateTime(): Carbon
    {
        return $this->dateTime;
    }
}
