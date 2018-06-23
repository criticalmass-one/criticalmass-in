<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Profile\ParticipationTable;

use AppBundle\Entity\Participation;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;

class TableGenerator implements TableGeneratorInterface
{
    /** @var ParticipationTable $table */
    protected $table;

    /** @var User $user */
    protected $user;

    /** @var Registry $registry */
    protected $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;

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

    public function getTable(): ParticipationTable
    {
        return $this->table;
    }
}
