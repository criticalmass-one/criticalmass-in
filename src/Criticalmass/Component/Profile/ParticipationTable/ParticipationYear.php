<?php declare(strict_types=1);

namespace Criticalmass\Component\Profile\ParticipationTable;


use Criticalmass\Bundle\AppBundle\Entity\Participation;

class ParticipationYear
{
    /** @var int $year */
    protected $year;

    /** @var array $monthList */
    protected $monthList;

    public function __construct(int $year)
    {
        $this->year = $year;

        $this->initMonthList();
    }

    protected function initMonthList(): ParticipationYear
    {
        for ($month = 1; $month <= 12; ++$month) {
            $this->monthList[$month] = new ParticipationMonth($this->year, $month);
        }

        return $this;
    }

    public function addParticipation(Participation $participation): ParticipationYear
    {
        $ride = $participation->getRide();
        $dateTime = $ride->getDateTime();
        $month = (int) $dateTime->format('n');

        $this->monthList[$month][$dateTime];

        return $this;
    }
}
