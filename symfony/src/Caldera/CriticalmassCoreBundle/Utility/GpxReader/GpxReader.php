<?php

namespace Caldera\CriticalmassCoreBundle\Utility\GpxReader;

use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxCoordLoop\GpxCoordLoop;
use Caldera\CriticalmassTrackBundle\Entity\Track;

class GpxReader {
    protected $path;
    protected $rawFileContent;
    protected $simpleXml;

    public function loadFile($path)
    {
        $this->path = $path;
        $this->rawFileContent = file_get_contents($path);
        $result = true;

        try {
            $this->simpleXml = new \SimpleXMLElement($this->rawFileContent);
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }
    public function loadString($content)
    {
        $this->rawFileContent = $content;

        $this->simpleXml = new \SimpleXMLElement($this->rawFileContent);
    }

    public function loadTrack(Track $track)
    {
        if (!$track->getGpx())
        {
            $track->loadTrack();
        }

        $this->rawFileContent = $track->getGpx();

        $this->simpleXml = new \SimpleXMLElement($this->rawFileContent);
    }

    public function getCreationDateTime()
    {
        return new \DateTime($this->simpleXml->metadata->time);
    }

    public function getStartDateTime()
    {
        return new \DateTime($this->simpleXml->trk->trkseg->trkpt[0]->time);
    }

    public function getEndDateTime()
    {
        return new \DateTime($this->simpleXml->trk->trkseg->trkpt[count($this->simpleXml->trk->trkseg->trkpt) - 1]->time);
    }

    public function countPoints()
    {
        return count($this->simpleXml->trk->trkseg->trkpt);
    }

    public function getMd5Hash()
    {
        return md5($this->rawFileContent);
    }

    public function getFileContent()
    {
        return $this->rawFileContent;
    }

    public function getLatitudeOfPoint($n)
    {
        return (double) $this->simpleXml->trk->trkseg->trkpt[$n]['lat'];
    }

    public function getLongitudeOfPoint($n)
    {
        return (double) $this->simpleXml->trk->trkseg->trkpt[$n]['lon'];
    }

    public function getTimestampOfPoint($n)
    {
        return $this->simpleXml->trk->trkseg->trkpt[$n]->time;
    }
    
    public function getDateTimeOfPoint($n)
    {
        return new \DateTime($this->getTimestampOfPoint($n));
    }

    public function getTimeOfPoint($n)
    {
        return $this->simpleXml->trk->trkseg->trkpt[$n]->time;
    }

    public function getRootNode()
    {
        return $this->simpleXml;
    }

    public function generateJsonArray()
    {
        $result = array();

        $counter = 0;

        foreach ($this->simpleXml->trk->trkseg->trkpt as $point)
        {
            $result[] = array('lat' => (float) $point['lat'], 'lng' => (float) $point['lon']);//'['.$point['lat'].','.$point['lon'].']';

            ++$counter;

            if ($counter > 20)
            {
                break;
            }
        }

        return $result;
    }

    public function generateJsonDateTimeArray($skip = 0)
    {
        $result = '[';

        $first = true;
        $counter = 0;

        foreach ($this->simpleXml->trk->trkseg->trkpt as $point)
        {
            if ($counter == $skip) {
                if (!$first)
                {
                    $result .= ', ';
                }
                
                $result .= '{ "dateTime": "2015-02-02", "lat": "'.((float)$point['lat']).'", "lng": "'.((float)$point['lon']).'" }';
                
                $counter = 0;
                $first = false;
            }
            else
            {
                ++$counter;
            }
        }

        $result .= ']';
        
        return $result;
    }

    /**
     * The earth is flat, stupid. As we struggle with PHP and it’s acos calculations we assume the earth to be flat, so
     * we can use Pythagoras here. As we have only small distances about twenty or thirty kilometres, this works well
     * enough. This calculation will fail with wrong distances when a Critical Mass rides from Paris to Berlin or does
     * even larger distances.
     *
     * Don’t show this your kids.
     */
    public function calculateDistance()
    {
        $distance = (float) 0.0;

        $index = 1;

        $firstCoord = $this->simpleXml->trk->trkseg->trkpt[0];

        while ($index < $this->countPoints())
        {
            $secondCoord = $this->simpleXml->trk->trkseg->trkpt[$index];
            
            $dx = 71.5 * ((float) $firstCoord['lon'] - (float) $secondCoord['lon']);
            $dy = 111.3 * ((float) $firstCoord['lat'] - (float) $secondCoord['lat']);

            $value = (float) sqrt($dx * $dx + $dy * $dy);

            $distance += $value;

            $firstCoord = $secondCoord;

            ++$index;
        }

        return (float) round($distance, 2);
    }
    
    public function findCoordNearDateTime(\DateTime $dateTime)
    {
        $gcl = new GpxCoordLoop($this);
        $result = $gcl->execute($dateTime);
        
        return array('latitude' => $this->getLatitudeOfPoint($result), 'longitude' => $this->getLongitudeOfPoint($result));
    }
}