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

        if (file_exists($this->getPath().$this->getFilename()))
        {
            $image = imagecreatefrompng($this->getPath().$this->getFilename());
        }
        else
        {
            $image = imagecreatetruecolor($this->tile->getSize(), $this->tile->getSize());
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);
        }

        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        $pixel = $this->tile->popPixel();

        while ($pixel != null)
        {
            //imagesetpixel($image, $pixel->getX(), $pixel->getY(), $black);
            imageellipse($image, $pixel->getX() - 1, $pixel->getY() - 1, 3, 3, $black);
            $pixel = $this->tile->popPixel();
        }

        ob_start();
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagepng($image);
        $this->imageFileContent = ob_get_contents();
        ob_end_clean();

        imagedestroy($image);
    }

    public function saveTile()
    {
        $pathname = $this->getPath();
        $filename = $this->getFilename();

        @mkdir($pathname, 0777, true);

        $handle = fopen($pathname.$filename, "w");
        fwrite($handle, $this->imageFileContent);
        fclose($handle);
    }
}