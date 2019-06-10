<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\ViewInterface;

use App\Entity\User;

interface ViewEntity
{
    public function getId(): ?int;

    public function setId(int $id): ViewEntity;

    public function setUser(User $user = null): ViewEntity;

    public function getUser(): ?User;

    public function setDateTime(\DateTime $dateTime): ViewEntity;

    public function getDateTime(): \DateTime;
}
