<?php declare(strict_types=1);

namespace AppBundle\Event\Participation;

use AppBundle\Entity\Participation;

abstract class AbstractParticipationEvent
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
