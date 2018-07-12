<?php

namespace App\EntityInterface;

use App\Entity\User;

interface ViewInterface
{
    public function getId(): ?int;

    public function setUser(User $user = null): ViewInterface;

    public function getUser(): ?User;

    public function setDateTime(\DateTime $dateTime): ViewInterface;

    public function getDateTime(): \DateTime;
}
