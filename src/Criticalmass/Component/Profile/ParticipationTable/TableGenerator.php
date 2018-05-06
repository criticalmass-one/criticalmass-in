<?php declare(strict_types=1);

namespace Criticalmass\Component\Profile\ParticipationTable;

use Criticalmass\Bundle\AppBundle\Entity\User;
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
        return $this;
    }

    public function getTable(): ParticipationTable
    {
        return $this->table;
    }
}
