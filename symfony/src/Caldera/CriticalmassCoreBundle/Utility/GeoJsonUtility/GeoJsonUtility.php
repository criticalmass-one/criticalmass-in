<?php

namespace Caldera\CriticalmassCoreBundle\Utility\GeoJsonUtility;


use Caldera\CriticalmassCoreBundle\Entity\Track;

class GeoJsonUtility
{
    private $json;

    public function loadFile($filename)
    {
        $this->json = file_get_contents($filename);
    }

    public function saveFile($filename)
    {
        $handle = fopen($filename, "a");
        fwrite($handle, $this->json);
        fclose($handle);
    }

    public function addTrackAsPolyline(Track $track)
    {

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