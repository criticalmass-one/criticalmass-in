<?php

namespace Caldera\CriticalmassStatisticBundle\Utility\Heatmap;

use Caldera\CriticalmassGlympseBundle\Entity\Ticket;
use Caldera\CriticalmassStatisticBundle\Entity\Heatmap;

class TraceTilePrinter extends AbstractTilePrinter {
    protected $ticket;

    public function __construct(Tile $tile, Heatmap $heatmap, Ticket $ticket)
    {
        $this->tile = $tile;
        $this->heatmap = $heatmap;
        $this->ticket = $ticket;
    }

    public function printTile()
    {
        //$image = imagecreatetruecolor($this->tile->getSize(), $this->tile->getSize());
        //$image = imagecreatefrompng('/Applications/XAMPP/htdocs/kachel.png');

        if (file_exists($this->getPath().$this->getFilename()))
        {
            $oldImage = imagecreatefrompng($this->getPath().$this->getFilename());
            $image = imagecreatefrompng($this->getPath().$this->getFilename());
        }
        else
        {
            $image = imagecreatetruecolor($this->tile->getSize(), $this->tile->getSize());
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);
        }

        $pixel = $this->tile->popPixel();

        while ($pixel != null)
        {
            $color = imagecolorallocate($image, $this->ticket->getColorRed(), $this->ticket->getColorGreen(), $this->ticket->getColorBlue());

            imagefilledellipse($image, $pixel->getX(), $pixel->getY(), 1, 1, $color);
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