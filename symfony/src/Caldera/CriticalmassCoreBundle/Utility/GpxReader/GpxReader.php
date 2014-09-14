<?php
/**
 * Created by PhpStorm.
 * User: maltehuebner
 * Date: 14.09.14
 * Time: 02:01
 */

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

    public function generateJson()
    {
        $result = array();

        foreach ($this->simpleXml->trk->trkseg->trkpt as $point)
        {
            $result[] = '['.$point['lat'].','.$point['lon'].']';
        }

        return '['.implode($result, ',').']';
    }
}