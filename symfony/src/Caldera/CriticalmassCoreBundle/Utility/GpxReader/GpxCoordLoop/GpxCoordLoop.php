<?php

namespace Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxCoordLoop;

use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;

class GpxCoordLoop
{
    protected $gpxReader;
    protected $startIndex;
    protected $endIndex;
    
    public function __construct(GpxReader $gpxReader)
    {
        $this->gpxReader = $gpxReader;
        $this->startIndex = 0;
        $this->endIndex = $this->gpxReader->countPoints();
    }
    
    public function execute(\DateTime $dateTime)
    {
        $found = false;

        while (!$found)
        {
            $mid = $this->startIndex + (int) floor(($this->endIndex - $this->startIndex) / 2);

            //echo $this->startIndex." : ".$mid." : ".$this->endIndex."    ";

            if ($this->gpxReader->getDateTimeOfPoint($mid)->format('U') < $dateTime->format('U'))
            {
                $this->startIndex = $mid;
            }
            elseif ($this->gpxReader->getDateTimeOfPoint($mid)->format('U') > $dateTime->format('U'))
            {
                $this->endIndex = $mid;
            }
            else
            {
                return $mid;
            }
            
            if ($this->endIndex - $this->startIndex < 2)
            {
                return $mid;
            }
        }
    }
    
}