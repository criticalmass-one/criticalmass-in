<?php declare(strict_types=1);

namespace App\Validator;

use App\Validator\Constraint\ValidGpxFile;
use phpGPX\phpGPX;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidGpxFileValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidGpxFile) {
            throw new UnexpectedTypeException($constraint, ValidGpxFile::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof File) {
            throw new UnexpectedTypeException($value, File::class);
        }

        $path = $value->getPathname();

        try {
            $phpGpx = new phpGPX();
            $gpxFile = $phpGpx->load($path);
        } catch (\Throwable) {
            $this->context->buildViolation($constraint->invalidGpxMessage)->addViolation();

            return;
        }

        if (empty($gpxFile->tracks)) {
            $this->context->buildViolation($constraint->noTrackStructureMessage)->addViolation();

            return;
        }

        $hasSegmentsWithPoints = false;

        foreach ($gpxFile->tracks as $track) {
            foreach ($track->segments as $segment) {
                if (!empty($segment->points)) {
                    $hasSegmentsWithPoints = true;
                    break 2;
                }
            }
        }

        if (!$hasSegmentsWithPoints) {
            $this->context->buildViolation($constraint->noTrackStructureMessage)->addViolation();

            return;
        }

        $totalPoints = 0;

        foreach ($gpxFile->tracks as $track) {
            foreach ($track->segments as $segment) {
                $totalPoints += count($segment->points);
            }
        }

        if ($totalPoints <= 50) {
            $this->context->buildViolation($constraint->notEnoughPointsMessage)->addViolation();

            return;
        }

        foreach ($gpxFile->tracks as $track) {
            foreach ($track->segments as $segment) {
                foreach ($segment->points as $point) {
                    if (null === $point->latitude || null === $point->longitude
                        || $point->latitude < -90.0 || $point->latitude > 90.0
                        || $point->longitude < -180.0 || $point->longitude > 180.0) {
                        $this->context->buildViolation($constraint->invalidCoordinatesMessage)->addViolation();

                        return;
                    }
                }
            }
        }

        foreach ($gpxFile->tracks as $track) {
            foreach ($track->segments as $segment) {
                foreach ($segment->points as $point) {
                    if (null === $point->time) {
                        $this->context->buildViolation($constraint->invalidTimestampMessage)->addViolation();

                        return;
                    }
                }
            }
        }
    }
}
