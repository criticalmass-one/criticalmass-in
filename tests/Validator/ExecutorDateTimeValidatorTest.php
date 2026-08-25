<?php declare(strict_types=1);

namespace Tests\Validator;

use App\Model\RideGenerator\CycleExecutable;
use App\Validator\Constraint\ExecutorDateTime;
use App\Validator\ExecutorDateTimeValidator;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<ExecutorDateTimeValidator>
 */
final class ExecutorDateTimeValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ExecutorDateTimeValidator
    {
        return new ExecutorDateTimeValidator();
    }

    private function executable(?string $from, ?string $until): CycleExecutable
    {
        return (new CycleExecutable())
            ->setFromDate($from ? new \DateTime($from) : null)
            ->setUntilDate($until ? new \DateTime($until) : null);
    }

    #[Test]
    public function fromBeforeUntilIsValid(): void
    {
        $this->validator->validate($this->executable('2024-01-01', '2024-12-31'), new ExecutorDateTime());

        $this->assertNoViolation();
    }

    #[Test]
    public function sameDayIsValid(): void
    {
        $this->validator->validate($this->executable('2024-05-31', '2024-05-31'), new ExecutorDateTime());

        $this->assertNoViolation();
    }

    #[Test]
    public function fromAfterUntilAddsViolationOnUntilDate(): void
    {
        $constraint = new ExecutorDateTime();

        $this->validator->validate($this->executable('2024-12-31', '2024-01-01'), $constraint);

        $this->buildViolation($constraint->message)
            ->atPath('property.path.untilDate')
            ->assertRaised();
    }

    #[Test]
    public function missingDatesDoNotRaise(): void
    {
        $this->validator->validate($this->executable(null, null), new ExecutorDateTime());

        $this->assertNoViolation();
    }

    #[Test]
    public function constraintTargetsTheWholeClass(): void
    {
        $constraint = new ExecutorDateTime();

        self::assertSame(ExecutorDateTime::CLASS_CONSTRAINT, $constraint->getTargets());
        self::assertSame(ExecutorDateTimeValidator::class, $constraint->validatedBy());
    }
}
