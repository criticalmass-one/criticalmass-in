<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Validator\Constraint;

use App\Criticalmass\DataQuery\Validator\BoundingBoxValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BoundingBoxValues extends Constraint
{
    public $message = 'Invalid values for Bounding Box Query.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return BoundingBoxValidator::class;
    }
}
