<?php declare(strict_types=1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractUserEvent extends Event
{
    /** @var User $user */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
