<?php declare(strict_types=1);

namespace App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException;

class NoValidGpxStructureException extends TrackValidatorException
{
    protected $message = 'Die Gpx-Struktur deiner hochgeladenen Datei ist offenbar defekt. Bitte überprüfe noch einmal, ob du wirklich die richtige Datei hochgeladen hast.';
}
