<?php declare(strict_types=1);

namespace App\Event\Participation;

use App\Entity\Participation;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractParticipationEvent extends Event
{
    public function __construct(protected Participation $participation)
    {
    }

    public function getParticipation(): Participation
    {
        return $this->participation;
    }
}
