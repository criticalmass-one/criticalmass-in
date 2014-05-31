<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 31.05.14
 * Time: 16:27
 */

namespace Caldera\CriticalmassHeatmapBundle\Utility;


class PNGTilePrinter extends AbstractTilePrinter {
    public function printTile()
    {
        $image = imagecreatetruecolor($this->tile->getSize(), $this->tile->getSize());
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white);

        $pixel = $this->tile->popPixel();

        while ($pixel != null)
        {
            //echo $pixel->getX()." ".$pixel->getY()." ".$pixel->getValue()."<br />";
            imagesetpixel($image, $pixel->getX(), $pixel->getY(), $black);

            $pixel = $this->tile->popPixel();
        }

        imagepng($image);
        imagedestroy($image);
    }
}