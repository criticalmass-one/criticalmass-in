<?php

namespace AppBundle\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException;

class NoValidGpxStructureException extends TrackValidatorException
{
    protected $message = 'Die Gpx-Struktur deiner hochgeladenen Datei ist offenbar defekt.';
}
