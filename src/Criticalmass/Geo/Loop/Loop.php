<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Loop;

use App\Criticalmass\Geo\EntityInterface\PositionInterface;
use App\Criticalmass\Geo\PositionList\PositionListInterface;

class Loop implements LoopInterface
{
    /** @var \DateTimeZone $dateTimeZone */
    protected $dateTimeZone = null;

    /** @var PositionListInterface $positionList */
    protected $positionList;

    /** @var int $startIndex */
    protected $startIndex = 0;

    /** @var int $endIndex */
    protected $endIndex = 0;

    public function __construct()
    {

    }

    public function setPositionList(PositionListInterface $positionList): LoopInterface
    {
        $this->positionList = $positionList;

        return $this;
    }

    public function setDateTimeZone(\DateTimeZone $dateTimeZone): LoopInterface
    {
        $this->dateTimeZone = $dateTimeZone;

        return $this;
    }

    public function searchIndexForDateTime(\DateTimeInterface $dateTime): ?int
    {
        if (!$this->positionList || count($this->positionList) === 0) {
            return null;
        }

        $found = false;

        $this->startIndex = 0;
        $this->endIndex = count($this->positionList) - 1;

        if ($dateTime < $this->positionList->getStartDateTime()) {
            return $this->startIndex;
        }

        if ($dateTime > $this->positionList->getEndDateTime()) {
            return $this->endIndex;
        }

        while (!$found) {
            $mid = $this->startIndex + (int)floor(($this->endIndex - $this->startIndex) / 2);

            $midDateTime = $this->positionList->get($mid)->getDateTime();

            if ($this->dateTimeZone) {
                $midDateTime->setTimezone($this->dateTimeZone);
            }

            if ($midDateTime < $dateTime) {
                $this->startIndex = $mid;
            } elseif ($midDateTime > $dateTime) {
                $this->endIndex = $mid;
            } else {
                return $mid;
            }

            if ($this->endIndex - $this->startIndex < 2) {
                return $mid;
            }
        }
    }

    public function searchPositionForDateTime(\DateTime $dateTime): ?PositionInterface
    {
        $index = $this->searchIndexForDateTime($dateTime);

        if (!$index) {
            return null;
        }

        return $this->positionList->get($index);
    }
}
