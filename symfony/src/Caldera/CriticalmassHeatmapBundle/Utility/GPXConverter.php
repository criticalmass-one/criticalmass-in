<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 31.05.14
 * Time: 19:22
 */

namespace Caldera\CriticalmassHeatmapBundle\Utility;


class GPXConverter
{
    protected $gpxFileContent;
    protected $gpxXML;

    public function loadContentFromFile($filename)
    {
        $this->gpxFileContent = file_get_contents($filename);
    }

    public function loadContentFromString($content)
    {
        $this->gpxFileContent = $content;
    }

    public function parseContent()
    {
        $this->gpxXML = new \SimpleXMLElement($this->gpxFileContent);
    }

    public function getPathArray()
    {
        $pathArray = array();

        $startPosition = null;
        $endPosition = null;

        foreach ($this->gpxXML->trk->trkseg->trkpt as $trackPoint)
        {
            if ($startPosition == null)
            {
                $startPosition = new Position($trackPoint['lat'], $trackPoint['lon']);
            }
            else
            {
                $endPosition = new Position($trackPoint['lat'], $trackPoint['lon']);

                $pathArray[] = new Path($startPosition, $endPosition);

                $startPosition = $endPosition;
            }
        }

        return $pathArray;
    }
}