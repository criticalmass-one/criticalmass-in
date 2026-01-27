<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\ViewModel;

use Carbon\Carbon;

class View
{
    private int $entityId;

    private string $entityClassName;

    private ?int $userId = null;

    private Carbon $dateTime;

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): View
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function setEntityClassName(string $entityClassName): View
    {
        $this->entityClassName = $entityClassName;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId = null): View
    {
        $this->userId = $userId;

        return $this;
    }

    public function getDateTime(): Carbon
    {
        return $this->dateTime;
    }

    public function setDateTime(Carbon $dateTime): View
    {
        $this->dateTime = $dateTime;

        return $this;
    }
}
