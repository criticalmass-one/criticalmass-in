<?php declare(strict_types=1);

namespace AppBundle\Event\Participation;

class ParticipationDeletedEvent extends AbstractParticipationEvent
{
    const NAME = 'participation.deleted';
}
