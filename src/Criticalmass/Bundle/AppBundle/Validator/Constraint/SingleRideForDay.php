<?php

namespace Criticalmass\Bundle\AppBundle\Validator\Constraint;

use AppBundle\Validator\SingleRideForDayValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SingleRideForDay extends Constraint
{
    public $message = 'Für diesen Tag wurde bereits eine Tour angelegt.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return SingleRideForDayValidator::class;
    }
}
