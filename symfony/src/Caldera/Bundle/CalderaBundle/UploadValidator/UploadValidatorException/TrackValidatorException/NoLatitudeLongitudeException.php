<?php

namespace Caldera\Bundle\CalderaBundle\UploadValidator\UploadValidatorException\TrackValidatorException;


class NoLatitudeLongitudeException extends TrackValidatorException
{
    protected $message = 'Deine Gpx-Datei enthält leider keine oder defekte Koordinaten.';
}