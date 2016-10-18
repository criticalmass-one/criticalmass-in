<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\Tile;

class OsmTile
{
    protected $pixelWidth = 256;
    protected $pixelHeight = 256;

    protected $x;
    protected $y;
    protected $z;

    protected $url;
    protected $image;

    public function __construct($x, $y, $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function createImage()
    {
        $this->image = imagecreatefrompng($this->url);
    }

    public function getImage()
    {
        return $this->image;
    }

    public function saveAsPng($filename)
    {
        imagepng($this->image, $filename);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}