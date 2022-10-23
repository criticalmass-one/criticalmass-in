<?php declare(strict_types=1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractUserEvent extends Event
{
    public function __construct(protected User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
