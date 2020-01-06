<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\Validator;

use App\Criticalmass\RideGenerator\ExecuteGenerator\CycleExecutable;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ExecutorDateTimeValidator extends ConstraintValidator
{
    /**
     * @var CycleExecutable $executeable
     */
    public function validate($executeable, Constraint $constraint): void
    {
        if ($executeable->getFromDate() > $executeable->getUntilDate()) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->atPath('untilDate')
                ->addViolation();
        }
    }
}
