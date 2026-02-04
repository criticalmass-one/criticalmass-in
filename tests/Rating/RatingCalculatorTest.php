<?php declare(strict_types=1);

namespace Tests\Rating;

use App\Criticalmass\Rating\Calculator\RatingCalculator;
use App\Entity\Rating;
use App\Entity\Ride;
use App\Repository\RatingRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RatingCalculatorTest extends TestCase
{
    public function testEmptyRating(): void
    {
        $calculator = $this->createCalculatorWithRatings([]);

        $this->assertNull($calculator->calculateRide(new Ride()));
    }

    public function testOneRating(): void
    {
        $calculator = $this->createCalculatorWithRatings([4]);

        $this->assertEquals(4.0, $calculator->calculateRide(new Ride()));
    }

    public function testTwoRatings(): void
    {
        $calculator = $this->createCalculatorWithRatings([4, 2]);

        $this->assertEquals(3.0, $calculator->calculateRide(new Ride()));
    }

    public function testThreeRatings(): void
    {
        $calculator = $this->createCalculatorWithRatings([4, 2, 5]);

        $this->assertEquals(3.67, $calculator->calculateRide(new Ride()));
    }

    public function testFourRatings(): void
    {
        $calculator = $this->createCalculatorWithRatings([4, 2, 5, 5]);

        $this->assertEquals(4.0, $calculator->calculateRide(new Ride()));
    }

    public function testAllFiveStars(): void
    {
        $calculator = $this->createCalculatorWithRatings([5, 5, 5, 5, 5]);

        $this->assertEquals(5.0, $calculator->calculateRide(new Ride()));
    }

    public function testAllOneStars(): void
    {
        $calculator = $this->createCalculatorWithRatings([1, 1, 1, 1, 1]);

        $this->assertEquals(1.0, $calculator->calculateRide(new Ride()));
    }

    public function testMixedExtremeRatings(): void
    {
        $calculator = $this->createCalculatorWithRatings([1, 5]);

        $this->assertEquals(3.0, $calculator->calculateRide(new Ride()));
    }

    public function testSingleMinimumRating(): void
    {
        $calculator = $this->createCalculatorWithRatings([1]);

        $this->assertEquals(1.0, $calculator->calculateRide(new Ride()));
    }

    public function testSingleMaximumRating(): void
    {
        $calculator = $this->createCalculatorWithRatings([5]);

        $this->assertEquals(5.0, $calculator->calculateRide(new Ride()));
    }

    public function testAverageRoundingDown(): void
    {
        // 1 + 2 = 3, 3/2 = 1.5
        $calculator = $this->createCalculatorWithRatings([1, 2]);

        $this->assertEquals(1.5, $calculator->calculateRide(new Ride()));
    }

    public function testAverageRoundingToTwoDecimals(): void
    {
        // 1 + 1 + 2 = 4, 4/3 = 1.333...
        $calculator = $this->createCalculatorWithRatings([1, 1, 2]);

        $this->assertEquals(1.33, $calculator->calculateRide(new Ride()));
    }

    public function testLargeNumberOfRatings(): void
    {
        $ratings = array_fill(0, 100, 4);
        $calculator = $this->createCalculatorWithRatings($ratings);

        $this->assertEquals(4.0, $calculator->calculateRide(new Ride()));
    }

    public function testLargeNumberOfMixedRatings(): void
    {
        // 50 ratings of 3 and 50 ratings of 4 = average 3.5
        $ratings = array_merge(array_fill(0, 50, 3), array_fill(0, 50, 4));
        $calculator = $this->createCalculatorWithRatings($ratings);

        $this->assertEquals(3.5, $calculator->calculateRide(new Ride()));
    }

    #[DataProvider('ratingAverageProvider')]
    public function testRatingAverages(array $ratings, float $expectedAverage): void
    {
        $calculator = $this->createCalculatorWithRatings($ratings);

        $this->assertEquals($expectedAverage, $calculator->calculateRide(new Ride()));
    }

    public static function ratingAverageProvider(): array
    {
        return [
            'single 3' => [[3], 3.0],
            'two 3s' => [[3, 3], 3.0],
            'ascending' => [[1, 2, 3, 4, 5], 3.0],
            'descending' => [[5, 4, 3, 2, 1], 3.0],
            'weighted low' => [[1, 1, 1, 5], 2.0],
            'weighted high' => [[5, 5, 5, 1], 4.0],
            'all 2s' => [[2, 2, 2, 2], 2.0],
            'all 3s' => [[3, 3, 3], 3.0],
            'all 4s' => [[4, 4, 4, 4, 4], 4.0],
            '3.5 average' => [[3, 4], 3.5],
            '4.5 average' => [[4, 5], 4.5],
            '2.5 average' => [[2, 3], 2.5],
            'complex average 1' => [[1, 2, 3, 4, 5, 5, 5], 3.57],
            'complex average 2' => [[1, 1, 1, 1, 5, 5, 5, 5], 3.0],
        ];
    }

    public function testCalculatorReturnsFloatType(): void
    {
        $calculator = $this->createCalculatorWithRatings([4]);

        $result = $calculator->calculateRide(new Ride());

        $this->assertIsFloat($result);
    }

    public function testCalculatorReturnsNullForNoRatings(): void
    {
        $calculator = $this->createCalculatorWithRatings([]);

        $result = $calculator->calculateRide(new Ride());

        $this->assertNull($result);
    }

    private function createCalculatorWithRatings(array $ratingValues): RatingCalculator
    {
        $ratings = array_map(
            fn(int $value) => (new Rating())->setRating($value),
            $ratingValues
        );

        $repository = $this->createMock(RatingRepository::class);
        $repository
            ->method('findBy')
            ->willReturn($ratings);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->method('getRepository')
            ->willReturn($repository);

        return new RatingCalculator($registry);
    }
}
