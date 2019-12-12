<?php declare(strict_types=1);

namespace App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException;

class NoXmlException extends TrackValidatorException
{
    protected $message = 'Du hast leider eine ungültige Datei hochgeladen.';
}
