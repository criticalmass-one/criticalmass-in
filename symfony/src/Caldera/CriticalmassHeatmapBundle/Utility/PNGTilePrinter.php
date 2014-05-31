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
        //$image = imagecreatetruecolor($this->tile->getSize(), $this->tile->getSize());
        //$image = imagecreatefrompng('/Applications/XAMPP/htdocs/kachel.png');
        $image = imagecreatefrompng('http://c.tile.openstreetmap.org/'.$this->tile->getOsmZoom().'/'.$this->tile->getOsmXTile().'/'.$this->tile->getOsmYTile().'.png');
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white);

        $pixel = $this->tile->popPixel();

        $counter = 0;
        while ($pixel != null && $counter < 10000)
        {
            ++$counter;
            //echo $pixel->getX()." ".$pixel->getY()."<br />";
            imagesetpixel($image, $pixel->getX(), $pixel->getY(), $black);

            $pixel = $this->tile->popPixel();
        }

        ob_start();
        imagepng($image);
        $this->imageFileContent = ob_get_contents();
        ob_end_clean();

        imagedestroy($image);
    }
}