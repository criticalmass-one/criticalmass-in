<?php

namespace Caldera\CriticalmassCoreBundle\Utility\GpxReader;

class GpxReader {
    protected $path;
    protected $rawFileContent;
    protected $simpleXml;

    public function loadFile($path)
    {
        $this->path = $path;
        $this->rawFileContent = file_get_contents($path);

        $this->simpleXml = new \SimpleXMLElement($this->rawFileContent);
    }

    public function loadString($content)
    {
        $this->rawFileContent = $content;

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
        return md5_file($this->path);
    }

    public function getFileContent()
    {
        return $this->rawFileContent;
    }

    public function getLatitudeOfPoint($n)
    {
        return $this->simpleXml->trk->trkseg->trkpt[$n]['lat'];
    }

    public function getLongitudeOfPoint($n)
    {
        return $this->simpleXml->trk->trkseg->trkpt[$n]['lon'];
    }

    public function getTimestampOfPoint($n)
    {
        return $this->simpleXml->trk->trkseg->trkpt[$n]->time;
    }
    
    public function getDateTimeOfPoint($n)
    {
        return new \DateTime(str_replace("T", " ", str_replace("Z", "", $this->getTimestampOfPoint($n))));
    }

    public function generateJson()
    {
        $result = array();

        foreach ($this->simpleXml->trk->trkseg->trkpt as $point)
        {
            $result[] = '['.$point['lat'].','.$point['lon'].']';
        }

        return '['.implode($result, ',').']';
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

        return (float) $distance;
    }
}