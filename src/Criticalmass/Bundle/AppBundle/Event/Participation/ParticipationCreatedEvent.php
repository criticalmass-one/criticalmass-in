<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Event\Participation;

use Criticalmass\Bundle\AppBundle\Entity\Participation;
use Symfony\Component\EventDispatcher\Event;

class ParticipationCreatedEvent extends Event
{
    const NAME = 'participation.created';

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
