<?php

namespace Caldera\CriticalmassHeatmapBundle\Utility;


class Tile {
    protected $pixelList;
    protected $size = 256;

    public function dropPath(Path $path)
    {
        $this->addPixel(new Pixel(1, 2));
        $this->addPixel(new Pixel(1, 2));
        $this->addPixel(new Pixel(1, 3));
        $this->addPixel(new Pixel(1, 4));
        $this->addPixel(new Pixel(1, 5));
    }

    public function getSize()
    {
        return $this->size;
    }

    protected  function addPixel(Pixel $pixel)
    {
        $this->pixelList[] = $pixel;
    }

    public function popPixel()
    {
        if (count($this->pixelList) > 0)
        {
            return array_pop($this->pixelList);
        }

        return null;
    }
}