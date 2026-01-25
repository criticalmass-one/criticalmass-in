<?php declare(strict_types=1);

namespace App\Message;

class CountViewMessage
{
    public function __construct(
        private readonly int $entityId,
        private readonly string $entityClassName,
        private readonly ?int $userId,
        private readonly \DateTimeInterface $dateTime
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

    public function getDateTime(): \DateTimeInterface
    {
        return $this->dateTime;
    }
}
