<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Validator;

use App\Criticalmass\DataQuery\Query\BoundingBoxQuery;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BoundingBoxValidator extends ConstraintValidator
{
    /**
     * @var BoundingBoxQuery $boundingBoxQuery
     */
    public function validate($boundingBoxQuery, Constraint $constraint): void
    {
        if (!$boundingBoxQuery->hasNorthLatitude() || !$boundingBoxQuery->hasSouthLatitude() || !$boundingBoxQuery->hasWestLongitude() || !$boundingBoxQuery->hasEastLongitude()) {
            return;
        }

        if ($boundingBoxQuery->getNorthLatitude() <= $boundingBoxQuery->getSouthLatitude()) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }

        if ($boundingBoxQuery->getWestLongitude() >= $boundingBoxQuery->getEastLongitude()) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
