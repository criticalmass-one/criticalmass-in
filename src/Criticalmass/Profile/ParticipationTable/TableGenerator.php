<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\ParticipationTable;

use App\Entity\Participation;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class TableGenerator implements TableGeneratorInterface
{
    /** @var ParticipationTable $table */
    protected $table;

    /** @var User $user */
    protected $user;

    public function __construct(protected ManagerRegistry $registry)
    {
        $this->table = new ParticipationTable();
    }

    public function setUser(User $user): TableGenerator
    {
        $this->user = $user;

        return $this;
    }

    public function generate(): TableGenerator
    {
        $participationList = $this->registry->getRepository(Participation::class)->findByUser($this->user, true);

        foreach ($participationList as $participation) {
            $this->table->addParticipation($participation);
        }

        return $this;
    }

    public function addParticipation(Participation $participation): TableGenerator
    {
        $this->table->addParticipation($participation);

        return $this;
    }

    public function getTable(): ParticipationTable
    {
        return $this->table;
    }
}
