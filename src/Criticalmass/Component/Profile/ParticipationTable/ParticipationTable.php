<?php declare(strict_types=1);

namespace Criticalmass\Component\Profile\ParticipationTable;

use Criticalmass\Bundle\AppBundle\Entity\Participation;

class ParticipationTable
{
    /** @var array $yearList */
    protected $yearList = [];

    public function getYearList(): array
    {
        return $this->yearList;
    }

    public function addParticipation(Participation $participation): ParticipationTable
    {
        $ride = $participation->getRide();
        $year = (int) $ride->getDateTime()->format('Y');

        $this->createYearList($year);

        $this->yearList[$year]->addParticipation($participation);

        return $this;
    }

    protected function createYearList(int $fromYear): ParticipationTable
    {
        $untilYear = (new \DateTime())->format('Y');

        for ($year = $fromYear; $year <= $untilYear; ++$year) {
            if (array_key_exists($year, $this->yearList)) {
                $this->yearList[$year] = new ParticipationYear($year);
            }
        }

        return $this;
    }

}
