<?php declare(strict_types=1);

namespace Tests\Validator;

use App\Entity\City;
use App\Entity\Ride;
use App\Repository\RideRepository;
use App\Validator\Constraint\SingleRideForDay;
use App\Validator\SingleRideForDayValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

#[TestDox('SingleRideForDayValidator')]
class SingleRideForDayValidatorTest extends TestCase
{
    private ManagerRegistry $registry;
    private RideRepository $rideRepository;
    private ExecutionContextInterface $context;
    private SingleRideForDayValidator $validator;
    private SingleRideForDay $constraint;
    private City $city;

    protected function setUp(): void
    {
        $this->city = new City();
        $this->rideRepository = $this->createMock(RideRepository::class);
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getRepository')->with(Ride::class)->willReturn($this->rideRepository);

        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->constraint = new SingleRideForDay();

        $this->validator = new SingleRideForDayValidator($this->registry);
        $this->validator->initialize($this->context);
    }

    private function createRide(?\DateTime $dateTime = null, ?string $slug = null, ?int $id = null): Ride
    {
        $ride = new Ride();
        $ride->setCity($this->city);

        if ($dateTime) {
            $ride->setDateTime($dateTime);
        }

        if ($slug) {
            $ride->setSlug($slug);
        }

        if ($id) {
            $reflection = new \ReflectionProperty(Ride::class, 'id');
            $reflection->setValue($ride, $id);
        }

        return $ride;
    }

    #[TestDox('allows creating a date-based ride when no rides exist for that day')]
    public function testAllowsNewDateRideOnEmptyDay(): void
    {
        $this->rideRepository->method('findRidesForCity')->willReturn([]);
        $this->context->expects($this->never())->method('buildViolation');

        $ride = $this->createRide(new \DateTime('2030-06-15 19:00:00'));
        $this->validator->validate($ride, $this->constraint);
    }

    #[TestDox('rejects creating a second date-based ride on the same day')]
    public function testRejectsDuplicateDateRide(): void
    {
        $existingRide = $this->createRide(new \DateTime('2030-06-15 19:00:00'), null, 1);

        $this->rideRepository->method('findRidesForCity')->willReturn([$existingRide]);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->method('atPath')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->context->expects($this->once())->method('buildViolation')->willReturn($violationBuilder);

        $newRide = $this->createRide(new \DateTime('2030-06-15 20:00:00'));
        $this->validator->validate($newRide, $this->constraint);
    }

    #[TestDox('allows creating a date-based ride when only slug-based rides exist on that day')]
    public function testAllowsDateRideWhenOnlySlugRidesExist(): void
    {
        $slugRide = $this->createRide(new \DateTime('2030-06-15 19:00:00'), 'kidical-mass-june', 1);

        $this->rideRepository->method('findRidesForCity')->willReturn([$slugRide]);
        $this->context->expects($this->never())->method('buildViolation');

        $newRide = $this->createRide(new \DateTime('2030-06-15 20:00:00'));
        $this->validator->validate($newRide, $this->constraint);
    }

    #[TestDox('skips validation entirely for slug-based rides')]
    public function testSkipsValidationForSlugRides(): void
    {
        $this->rideRepository->expects($this->never())->method('findRidesForCity');
        $this->context->expects($this->never())->method('buildViolation');

        $slugRide = $this->createRide(new \DateTime('2030-06-15 19:00:00'), 'special-event');
        $this->validator->validate($slugRide, $this->constraint);
    }

    #[TestDox('allows creating a slug-based ride when a date-based ride exists on the same day')]
    public function testAllowsSlugRideOnDayWithDateRide(): void
    {
        $existingDateRide = $this->createRide(new \DateTime('2030-06-15 19:00:00'), null, 1);

        $this->rideRepository->expects($this->never())->method('findRidesForCity');
        $this->context->expects($this->never())->method('buildViolation');

        $newSlugRide = $this->createRide(new \DateTime('2030-06-15 20:00:00'), 'kidical-mass');
        $this->validator->validate($newSlugRide, $this->constraint);
    }

    #[TestDox('allows updating an existing date-based ride')]
    public function testAllowsUpdatingExistingDateRide(): void
    {
        $existingRide = $this->createRide(new \DateTime('2030-06-15 19:00:00'), null, 42);

        $this->rideRepository->method('findRidesForCity')->willReturn([$existingRide]);
        $this->context->expects($this->never())->method('buildViolation');

        // Updating the same ride (same id) — maxRidesPerDay = 1, foundRidesForSameDay = 1
        $this->validator->validate($existingRide, $this->constraint);
    }

    #[TestDox('rejects date-based ride when another date-based ride exists, ignoring slug rides')]
    public function testRejectsDateRideWithMixedExistingRides(): void
    {
        $existingDateRide = $this->createRide(new \DateTime('2030-06-15 19:00:00'), null, 1);
        $existingSlugRide = $this->createRide(new \DateTime('2030-06-15 19:00:00'), 'kidical-mass', 2);

        $this->rideRepository->method('findRidesForCity')->willReturn([$existingDateRide, $existingSlugRide]);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->method('atPath')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->context->expects($this->once())->method('buildViolation')->willReturn($violationBuilder);

        $newRide = $this->createRide(new \DateTime('2030-06-15 20:00:00'));
        $this->validator->validate($newRide, $this->constraint);
    }
}
