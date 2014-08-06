<?php

namespace Caldera\CriticalmassStatisticBundle\Utility\Heatmap;

use Caldera\CriticalmassCoreBundle\Entity\Track;
use Caldera\CriticalmassStatisticBundle\Entity\Heatmap;

class TraceTilePrinter extends AbstractTilePrinter {
    protected $ticket;
    protected $changed = false;

    public function __construct(Tile $tile, Heatmap $heatmap, Track $track)
    {
        $this->tile = $tile;
        $this->heatmap = $heatmap;
        $this->track = $track;
    }

    public function printTile()
    {
        if ($this->tile->hasPixel())
        {
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

            $pixel = $this->tile->popPixel();

            while ($pixel != null)
            {
                if ($this->track->getTicket())
                {
                    $ticket = $this->track->getTicket();
                    $color = imagecolorallocate($image, $ticket->getColorRed(), $ticket->getColorGreen(), $ticket->getColorBlue());
                }
                else
                {
                    $user = $this->track->getUser();
                    $color = imagecolorallocate($image, $user->getColorRed(), $user->getColorGreen(), $user->getColorBlue());
                }

                imagefilledellipse($image, $pixel->getX(), $pixel->getY(), 1, 1, $color);
                $pixel = $this->tile->popPixel();

                $this->changed = true;
            }

            if ($this->changed)
            {
                ob_start();
                imagealphablending($image, false);
                imagesavealpha($image, true);
                imagepng($image);
                $this->imageFileContent = ob_get_contents();
                ob_end_clean();
            }

            imagedestroy($image);
        }
    }

    public function saveTile()
    {
        if ($this->changed)
        {
            $pathname = $this->getPath();
            $filename = $this->getFilename();

            @mkdir($pathname, 0777, true);

            $handle = fopen($pathname.$filename, "w");
            fwrite($handle, $this->imageFileContent);
            fclose($handle);
        }
    }
}