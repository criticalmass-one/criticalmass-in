<?php declare(strict_types=1);

namespace Criticalmass\Component\Profile\ParticipationTable;

use Criticalmass\Bundle\AppBundle\Entity\Participation;

class ParticipationMonth implements \Countable
{
    protected $year;
    protected $month;

    protected $participationList = [];

    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function addParticipation(Participation $participation): ParticipationMonth
    {
        $ride = $participation->getRide();
        $dateTime = $ride->getDateTime();
        $day = (int)$dateTime->format('j');

        $this->participationList[$day] = $participation;

        return $this;
    }

    public function count(): int
    {
        return count($this->participationList);
    }

    public function getParticipationList(): array
    {
        return $this->participationList;
    }
}
