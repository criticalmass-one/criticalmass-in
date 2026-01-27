<?php declare(strict_types=1);

namespace App\Validator\Constraint;

use App\Validator\ValidGpxFileValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ValidGpxFile extends Constraint
{
    public string $invalidGpxMessage = 'Die hochgeladene Datei ist keine gueltige GPX-Datei.';
    public string $noTrackStructureMessage = 'Die GPX-Datei enthaelt keine auswertbare Trackstruktur.';
    public string $notEnoughPointsMessage = 'Die GPX-Datei enthaelt zu wenige Koordinaten fuer eine sinnvolle Auswertung.';
    public string $invalidCoordinatesMessage = 'Die GPX-Datei enthaelt fehlende oder ungueltige Koordinaten.';
    public string $invalidTimestampMessage = 'Die GPX-Datei enthaelt fehlende oder ungueltige Zeitstempel.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return ValidGpxFileValidator::class;
    }
}
