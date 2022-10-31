<?php declare(strict_types=1);

namespace App\Event\Participation;

class ParticipationCreatedEvent extends AbstractParticipationEvent
{
    final const NAME = 'participation.created';
}
