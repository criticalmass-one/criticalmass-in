<?php declare(strict_types=1);

namespace Criticalmass\Component\Profile\ParticipationTable;

use Criticalmass\Bundle\AppBundle\Entity\User;

interface TableGeneratorInterface
{
    public function setUser(User $user): TableGenerator;
    public function generate(): TableGenerator;
    public function getTable(): ParticipationTable;
}
