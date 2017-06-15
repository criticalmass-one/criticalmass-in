<?php

namespace UserBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use UserBundle\Validator\UniqueUsernameValidator;

/**
 * @Annotation
 */
class UniqueUsername extends Constraint
{
    public $message = 'Dieser Benutzername ist schon vergeben.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return UniqueUsernameValidator::class;
    }
}
