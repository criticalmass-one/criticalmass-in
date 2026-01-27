<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\ViewInterface;

use App\Entity\User;
use Carbon\Carbon;

interface ViewEntity
{
    public function getId(): ?int;

    public function setId(int $id): ViewEntity;

    public function setUser(?User $user = null): ViewEntity;

    public function getUser(): ?User;

    public function setDateTime(Carbon $dateTime): ViewEntity;

    public function getDateTime(): Carbon;
}
