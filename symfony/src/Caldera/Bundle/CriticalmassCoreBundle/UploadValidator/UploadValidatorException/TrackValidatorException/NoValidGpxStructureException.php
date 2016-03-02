<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\UploadValidator\UploadValidatorException\TrackValidatorException;

class NoValidGpxStructureException extends TrackValidatorException
{
    protected $message = 'Die Gpx-Struktur deiner hochgeladenen Datei ist offenbar defekt.';
}