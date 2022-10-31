<?php declare(strict_types=1);

namespace App\Event\User;

class UserColorChangedEvent extends AbstractUserEvent
{
    const NAME = 'user.color_changed';
}
