<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\GpxCoordLoop;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\GpxReader;

class GpxCoordLoop
{
    /** @var GpxReader $gpxReader */
    protected $gpxReader;

    /** @var int $startIndex */
    protected $startIndex;

    /** @var int $endIndex */
    protected $endIndex;

    /** @var \DateTimeZone */
    protected $dateTimeZone = null;

    public function __construct(GpxReader $gpxReader)
    {
        $this->gpxReader = $gpxReader;
        $this->startIndex = 0;
        $this->endIndex = $this->gpxReader->countPoints();
    }

    public function setDateTimeZone(\DateTimeZone $dateTimeZone = null)
    {
        $this->dateTimeZone = $dateTimeZone;

        return $this;
    }

    public function execute(\DateTime $dateTime)
    {
        $found = false;

        while (!$found) {
            $mid = $this->startIndex + (int)floor(($this->endIndex - $this->startIndex) / 2);

            $midDateTime = $this->gpxReader->getDateTimeOfPoint($mid);

            if ($this->dateTimeZone) {
                $midDateTime->setTimezone($this->dateTimeZone);
            }

            if ($midDateTime->format('Y-m-d-H-i-s') < $dateTime->format('Y-m-d-H-i-s')) {
                $this->startIndex = $mid;
            } elseif ($midDateTime->format('Y-m-d-H-i-s') > $dateTime->format('Y-m-d-H-i-s')) {
                $this->endIndex = $mid;
            } else {
                return $mid;
            }

            if ($this->endIndex - $this->startIndex < 2) {
                return $mid;
            }
        }
    }
}