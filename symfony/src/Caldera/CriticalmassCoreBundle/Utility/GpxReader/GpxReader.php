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

    protected $xmlReader;

    public function loadFile($path)
    {
        $this->path = $path;

        $this->xmlReader = new \XMLReader($path);

    }
}