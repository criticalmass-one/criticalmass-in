<?php declare(strict_types=1);

namespace App\Event\Participation;

class ParticipationDeletedEvent extends AbstractParticipationEvent
{
    const NAME = 'participation.deleted';
}
