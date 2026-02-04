<?php declare(strict_types=1);

namespace Tests\Rating;

use App\Criticalmass\Rating\Calculator\RatingCalculator;
use App\Entity\Rating;
use App\Entity\Ride;
use App\Repository\RatingRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class RatingCalculatorTest extends TestCase
{
    public function testEmptyRating(): void
    {
        $ride = new Ride();
        $repository = $this->createMock(RatingRepository::class);
        $repository
            ->method('findBy')
            ->willReturn([]);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->method('getRepository')
            ->willReturn($repository);

        $ratingCalculator = new RatingCalculator($registry);

        $this->assertNull($ratingCalculator->calculateRide($ride));
    }

    public function testOneRating(): void
    {
        $ride = new Ride();
        $repository = $this->createMock(RatingRepository::class);
        $repository
            ->method('findBy')
            ->willReturn([
                (new Rating())->setRating(4),
            ]);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->method('getRepository')
            ->willReturn($repository);

        $ratingCalculator = new RatingCalculator($registry);

        $this->assertEquals(4, $ratingCalculator->calculateRide($ride));
    }

    public function testTwoRatings(): void
    {
        $ride = new Ride();
        $repository = $this->createMock(RatingRepository::class);
        $repository
            ->method('findBy')
            ->willReturn([
                (new Rating())->setRating(4),
                (new Rating())->setRating(2),
            ]);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->method('getRepository')
            ->willReturn($repository);

        $ratingCalculator = new RatingCalculator($registry);

        $this->assertEquals(3, $ratingCalculator->calculateRide($ride));
    }

    public function testThreeRatings(): void
    {
        $ride = new Ride();
        $repository = $this->createMock(RatingRepository::class);
        $repository
            ->method('findBy')
            ->willReturn([
                (new Rating())->setRating(4),
                (new Rating())->setRating(2),
                (new Rating())->setRating(5),
            ]);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->method('getRepository')
            ->willReturn($repository);

        $ratingCalculator = new RatingCalculator($registry);

        $this->assertEquals(3.67, $ratingCalculator->calculateRide($ride));
    }

    public function testFourRatings(): void
    {
        $ride = new Ride();
        $repository = $this->createMock(RatingRepository::class);
        $repository
            ->method('findBy')
            ->willReturn([
                (new Rating())->setRating(4),
                (new Rating())->setRating(2),
                (new Rating())->setRating(5),
                (new Rating())->setRating(5),
            ]);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->method('getRepository')
            ->willReturn($repository);

        $ratingCalculator = new RatingCalculator($registry);

        $this->assertEquals(4, $ratingCalculator->calculateRide($ride));
    }
}
