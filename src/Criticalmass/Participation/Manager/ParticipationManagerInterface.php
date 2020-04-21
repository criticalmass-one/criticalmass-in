<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\Manager;

use App\Entity\Participation;
use App\Entity\Ride;

interface ParticipationManagerInterface
{
    public function participate(Ride $ride, string $status): Participation;
}
