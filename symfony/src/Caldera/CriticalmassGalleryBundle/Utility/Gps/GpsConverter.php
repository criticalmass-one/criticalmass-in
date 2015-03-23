<?php
/**
 * Created by IntelliJ IDEA.
 * User: malte
 * Date: 04.02.15
 * Time: 17:13
 */

namespace Caldera\CriticalmassGalleryBundle\Utility\Gps;

class GpsConverter {

    protected function coordinateToDec($coordinate) {
        list($dividend, $divisor) = explode('/', $coordinate);

        if ($divisor == 0) {
            return 0;
        } else {
            return $dividend / $divisor;
        }
    }
    
    public function convert($value)
    {
        $deg = $this->coordinateToDec($value[0]);
        $min = $this->coordinateToDec($value[1]);
        $sec = $this->coordinateToDec($value[2]);

        return $deg + ((($min * 60) + ($sec)) / 3600);
    }
}