<?php declare(strict_types=1);

/*
 * Ein Platzhalter fuer ImagickException.
 *
 * Die Erweiterung ist in der Produktion installiert -- liip_imagine faehrt auf
 * `driver: imagick` --, lokal und in der CI aber nicht: Die CI laedt nur intl
 * und pdo_pgsql. Ohne diesen Platzhalter liesse sich die Behandlung eines
 * Imagick-Fehlers nirgends pruefen ausser auf dem Server selbst.
 *
 * Die echte Klasse erbt ebenfalls von Exception; mehr braucht der
 * Subscriber nicht von ihr, er prueft nur den Typ.
 */
if (!class_exists('ImagickException', false)) {
    class ImagickException extends \Exception
    {
    }
}
