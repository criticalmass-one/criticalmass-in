<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\UploadValidator\UploadValidatorException\TrackValidatorException;

class NoDateTimeException extends TrackValidatorException
{
    protected $message = 'Deine hochgeladene Datei enthält leider keine Zeitstempel.';
}