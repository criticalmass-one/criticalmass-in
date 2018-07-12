<?php

namespace AppBundle\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException;

class NotEnoughCoordsException extends TrackValidatorException
{
    protected $message = 'Deine Gpx-Datei enthält leider zu wenige Koordinaten für eine sinnvolle Verwendung.';
}
