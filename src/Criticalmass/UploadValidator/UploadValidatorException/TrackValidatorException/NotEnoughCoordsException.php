<?php declare(strict_types=1);

namespace App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException;

class NotEnoughCoordsException extends TrackValidatorException
{
    protected $message = 'Deine Gpx-Datei enthält leider zu wenige Koordinaten für eine sinnvolle Auswertung. Bitte lade eine Datei hoch, die mindestens 50 Koordinaten enthält.';
}
