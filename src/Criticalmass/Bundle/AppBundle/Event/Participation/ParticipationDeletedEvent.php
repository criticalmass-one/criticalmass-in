<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Event\Participation;

class ParticipationDeletedEvent extends AbstractParticipationEvent
{
    const NAME = 'participation.deleted';
}
