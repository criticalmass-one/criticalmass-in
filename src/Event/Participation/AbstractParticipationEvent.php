<?php declare(strict_types=1);

namespace App\Event\Participation;

use App\Entity\Participation;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractParticipationEvent extends Event
{
    /** @var Participation $participation */
    protected $participation;

    public function __construct(Participation $participation)
    {
        $this->participation = $participation;
    }

    public function getParticipation(): Participation
    {
        return $this->participation;
    }
}
