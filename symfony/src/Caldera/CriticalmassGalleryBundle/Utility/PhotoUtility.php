<?php

namespace Caldera\CriticalmassGalleryBundle\Utility;

use Caldera\CriticalmassGalleryBundle\Entity\Photos;
use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;

class PhotoUtility {

    public function makeSmallPhotoJPG($photo, $width, $height, $name) {
        $image = imagecreatefromjpeg($photo->getFilePath());
        list($old_width, $old_height) = getimagesize($photo->getFilePath());
        $photo->setSmallFile(imagecreatetruecolor($width, $height));
        imagecopyresampled($photo->getSmallFile(), $image, 0, 0, 0, 0, $width, $height, $old_width, $old_height);
        imagejpeg($photo->getSmallFile(), $photo->getUploadRootDir() . $photo->getId() . $name . ".jpg", 100);
    }

    public function makeSmallPhotoPNG($photo, $width, $height, $name) {
        $image = imagecreatefrompng($photo->getFilePath());
        list($old_width, $old_height) = getimagesize($photo->getFilePath());
        $photo->setSmallFile(imagecreatetruecolor($width, $height));
        imagecopyresampled($photo->getSmallFile(), $image, 0, 0, 0, 0, $width, $height, $old_width, $old_height);
        imagepng($photo->getSmallFile(), $photo->getUploadRootDir() . $photo->getId() . $name . ".png", 100);
    }

    public function getMetaInfos($photo) {
        $info = exif_read_data($photo->getFilePath(), 0, true);

        if (isset($info['GPS']['GPSLatitude']) && isset($info['GPS']['GPSLongitude'])) {
            $deg = $this->coordinateToDec($info['GPS']['GPSLatitude'][0]);
            $min = $this->coordinateToDec($info['GPS']['GPSLatitude'][1]);
            $sec = $this->coordinateToDec($info['GPS']['GPSLatitude'][2]);
            $photo->setLatitude($deg + ((($min * 60) + ($sec)) / 3600));
            $deg = $this->coordinateToDec($info['GPS']['GPSLongitude'][0]);
            $min = $this->coordinateToDec($info['GPS']['GPSLongitude'][1]);
            $sec = $this->coordinateToDec($info['GPS']['GPSLongitude'][2]);
            $photo->setLongitude($deg + ((($min * 60) + ($sec)) / 3600));
        }

        if (isset($info['GPS']['GPSTimeStamp']) && isset($info['GPS']['GPSDateStamp'])) {
            $photo->setDateTime(new \DateTime(str_replace(":", "-", $info['GPS']['GPSDateStamp'])
                . ' ' . preg_replace("#[/].*#", "", $info['GPS']['GPSTimeStamp'][0]) . ":" .
                preg_replace("#[/].*#", "", $info['GPS']['GPSTimeStamp'][1]) . ":" .
                preg_replace("#[/].*#", "", $info['GPS']['GPSTimeStamp'][2])));
        }
    }

    function coordinateToDec($coordinate) {
        list($dividend, $divisor) = split("/", $coordinate);
        if ($divisor == 0) {
            return 0;
        } else {
            return $dividend / $divisor;
        }
    }

    function xmlToDateTime($xml) {
        return new \DateTime(str_replace("T", " ", str_replace("Z", "", $xml)));
    }

    function timeDiffinSec($difference) {
        return $difference->format('%s') + 60 * $difference->format("%i") + 3600 * $difference->format("%H");
    }

    function interpolate($firstPoint, $secondPoint, $i, $j) {
        $n = $i + $j;
        return floatval($firstPoint * (($n - $i) / floatval($n))) +
               floatval($secondPoint * (($n - $j) / floatval($n)));
    }

    public function approximateCoordinates($photo, $track) {
        if (count($track)) {
            $gpxReader = new GpxReader();
            $gpxReader->loadString($track[0]->getGpx());
            $finished = 0;
            for ($i = 0; $i < ($gpxReader->countPoints() - 1) && !($finished); $i++) {
                $tmpDatetimePrev = $this->xmlToDateTime($gpxReader->getTimestampOfPoint($i));
                $tmpDatetimeSucc = $this->xmlToDateTime($gpxReader->getTimestampOfPoint($i + 1));
                if (($tmpDatetimePrev <= $photo->getDateTime()) &&
                    ($tmpDatetimeSucc >= $photo->getDateTime()))
                {
                    $timeDiffPrev = $this->timeDiffInSec($tmpDatetimePrev->diff($photo->getDateTime()));
                    $timeDiffSucc = $this->timeDiffinSec($tmpDatetimeSucc->diff($photo->getDateTime()));
                    $photo->setLatitude($this->interpolate($gpxReader->getLatitudeOfPoint($i),
                                                           $gpxReader->getLatitudeOfPoint($i+1),
                                                           $timeDiffPrev,
                                                           $timeDiffSucc));
                    $photo->setLongitude($this->interpolate($gpxReader->getLongitudeOfPoint($i),
                                                            $gpxReader->getLongitudeOfPoint($i+1),
                                                            $timeDiffPrev,
                                                            $timeDiffSucc));
                    $finished = 1;
                }
            }
        }
    }
}