<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Event\Participation;

use Criticalmass\Bundle\AppBundle\Entity\Participation;
use Symfony\Component\EventDispatcher\Event;

class ParticipationDeletedEvent extends Event
{
    const NAME = 'participation.deleted';

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
