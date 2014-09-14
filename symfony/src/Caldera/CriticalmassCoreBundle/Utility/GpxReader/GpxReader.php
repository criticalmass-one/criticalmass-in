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

    protected $simpleXml;

    public function loadFile($path)
    {
        $this->path = $path;
        $this->rawFileContent = file_get_contents($path);

        $this->simpleXml = new \SimpleXMLElement($this->rawFileContent);
    }
}