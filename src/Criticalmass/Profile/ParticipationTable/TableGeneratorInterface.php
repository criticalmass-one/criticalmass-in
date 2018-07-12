<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Profile\ParticipationTable;

use AppBundle\Entity\User;

interface TableGeneratorInterface
{
    public function setUser(User $user): TableGenerator;
    public function generate(): TableGenerator;
    public function getTable(): ParticipationTable;
}
