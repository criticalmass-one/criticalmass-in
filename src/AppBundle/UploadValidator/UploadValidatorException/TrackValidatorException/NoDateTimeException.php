<?php

namespace AppBundle\UploadValidator\UploadValidatorException\TrackValidatorException;

class NoDateTimeException extends TrackValidatorException
{
    protected $message = 'Deine hochgeladene Datei enthält leider keine oder defekte Zeitstempel.';
}