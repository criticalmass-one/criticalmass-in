<?php declare(strict_types=1);

namespace App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException;

class NoDateTimeException extends TrackValidatorException
{
    protected $message = 'Deine hochgeladene Datei enthält leider keine oder defekte Zeitstempel. Bitte versuche deinen Gpx-Track so zu exportieren, dass die Zeitstempel erhalten bleiben.';
}
