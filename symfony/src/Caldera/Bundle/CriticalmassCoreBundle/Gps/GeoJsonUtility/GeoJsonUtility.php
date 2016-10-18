<?php

namespace Caldera\CriticalmassCoreBundle\Utility\GeoJsonUtility;


use Caldera\CriticalmassCoreBundle\Entity\Track;
use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;

class GeoJsonUtility
{
    private $json;

    public function loadFile($filename)
    {
        $this->json = file_get_contents($filename);
    }

    public function saveFile($filename)
    {
        $handle = fopen($filename, "w+");
        fwrite($handle, 'var line = ' . $this->json . ';');
        fclose($handle);
    }

    public function addTrackAsPolyline(Track $track)
    {
        if (!$this->json) {
            $this->createTemplate();
        }

        $gr = new GpxReader();
        $gr->loadTrack($track);

        $json = json_decode($this->json, true);

        $feature = array();
        $feature['type'] = 'Feature';
        $feature['geometry']['type'] = 'LineString';
        $feature['geometry']['coordinates'] = $gr->generateJsonArray();

        array_push($json['features'], $feature);

        $this->json = json_encode($json, JSON_FORCE_OBJECT);
    }

    public function createTemplate()
    {
        $this->json = <<<EOT
{ "type": "FeatureCollection",
    "features": [
       ]
     }
EOT;
    }
} 