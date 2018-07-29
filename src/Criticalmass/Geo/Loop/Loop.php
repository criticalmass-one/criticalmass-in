<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Loop;

use Caldera\GeoBundle\EntityInterface\PositionInterface;
use Caldera\GeoBundle\PositionList\PositionListInterface;

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

    public function searchIndexForDateTime(\DateTimeInterface $dateTime): int
    {
        $found = false;

        $this->startIndex = 0;
        $this->endIndex = count($this->positionList);

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

    public function searchPositionForDateTime(\DateTime $dateTime): PositionInterface
    {
        $index = $this->searchIndexForDateTime($dateTime);

        return $this->positionList->get($index);
    }
}
