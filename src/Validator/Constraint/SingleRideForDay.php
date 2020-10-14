<?php declare(strict_types=1);

namespace App\Validator\Constraint;

use App\Validator\SingleRideForDayValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SingleRideForDay extends Constraint
{
    public $message = 'Für diesen Tag wurde bereits eine Tour angelegt.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return SingleRideForDayValidator::class;
    }
}
