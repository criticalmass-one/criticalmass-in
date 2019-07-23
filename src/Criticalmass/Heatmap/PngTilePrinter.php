<?php

namespace Caldera\CriticalmassStatisticBundle\Utility\Heatmap;

class PNGTilePrinter extends AbstractTilePrinter
{
    public function printTile()
    {
        //$image = imagecreatetruecolor($this->tile->getSize(), $this->tile->getSize());
        //$image = imagecreatefrompng('/Applications/XAMPP/htdocs/kachel.png');

        if (file_exists($this->getPath() . $this->getFilename())) {
            $oldImage = imagecreatefrompng($this->getPath() . $this->getFilename());
            $image = imagecreatefrompng($this->getPath() . $this->getFilename());
        } else {
            $image = imagecreatetruecolor($this->tile->getSize(), $this->tile->getSize());
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);
        }

        $pixel = $this->tile->popPixel();

        while ($pixel != null) {
            if (isset($oldImage)) {
                $rgb = imagecolorat($image, $pixel->getX(), $pixel->getY());

                $colorIndices = imagecolorsforindex($image, $rgb);

                $step = 64;

                if (($colorIndices['red'] <= 127) && ($colorIndices['green'] <= 127) && ($colorIndices['blue'] == 255)) {
                    $color = imagecolorallocate($image, $colorIndices['red'] + $step, $colorIndices['green'] + $step, $colorIndices['blue']);
                } else if (($colorIndices['red'] == 128) && ($colorIndices['green'] == 128) && ($colorIndices['blue'] >= 128)) {
                    $color = imagecolorallocate($image, $colorIndices['red'] + $step, $colorIndices['green'], $colorIndices['blue'] - $step);
                } else if (($colorIndices['red'] >= 128) && ($colorIndices['green'] == 128) && ($colorIndices['blue'] == 128)) {
                    $color = imagecolorallocate($image, $colorIndices['red'] + $step, $colorIndices['green'] - $step, $colorIndices['blue'] - $step);
                } else {
                    $color = imagecolorallocate($image, 0, 0, 255);
                }
            } else {
                $color = imagecolorallocate($image, 0, 0, 255);
            }

            imagefilledellipse($image, $pixel->getX() - 1, $pixel->getY() - 1, 3, 3, $color);
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

        $handle = fopen($pathname . $filename, "w");
        fwrite($handle, $this->imageFileContent);
        fclose($handle);
    }
}