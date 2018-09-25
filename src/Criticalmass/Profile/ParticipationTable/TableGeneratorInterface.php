<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\ParticipationTable;

use App\Entity\Participation;
use App\Entity\User;

interface TableGeneratorInterface
{
    public function setUser(User $user): TableGenerator;
    public function generate(): TableGenerator;
    public function getTable(): ParticipationTable;
    public function addParticipation(Participation $participation): TableGenerator;
}
