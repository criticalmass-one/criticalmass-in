<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage;

use App\Entity\User;

class View
{
    /** @var int $entityId */
    protected $entityId;

    /** @var string $entityClassName */
    protected $entityClassName;

    /** @var User $user */
    protected $user;

    /** @var \DateTime $dateTime */
    protected $dateTime;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): View
    {
        $this->user = $user;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): View
    {
        $this->dateTime = $dateTime;

        return $this;
    }
}