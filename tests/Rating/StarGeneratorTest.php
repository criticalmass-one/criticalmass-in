<?php declare(strict_types=1);

namespace Tests\Rating;

use App\Criticalmass\Rating\Calculator\RatingCalculatorInterface;
use App\Criticalmass\Rating\StarGenerator\StarGenerator;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StarGeneratorTest extends TestCase
{
    private const STAR_FULL = '<i class="fas fa-star"></i>';
    private const STAR_HALF = '<i class="fad fa-star-half-alt"></i>';
    private const STAR_EMPTY = '<i class="far fa-star"></i>';

    public function testNullRatingShowsFiveEmptyStars(): void
    {
        $starString = $this->generateStars(null);

        $expectedStars = str_repeat(self::STAR_EMPTY, 5);
        $this->assertEquals($expectedStars, $starString);
    }

    public function testZeroRatingShowsFiveEmptyStars(): void
    {
        $starString = $this->generateStars(0.0);

        $expectedStars = str_repeat(self::STAR_EMPTY, 5);
        $this->assertEquals($expectedStars, $starString);
    }

    public function testOneStarRating(): void
    {
        $starString = $this->generateStars(1.0);

        $expectedStars = self::STAR_FULL . str_repeat(self::STAR_EMPTY, 4);
        $this->assertEquals($expectedStars, $starString);
    }

    public function testTwoStarRating(): void
    {
        $starString = $this->generateStars(2.0);

        $expectedStars = str_repeat(self::STAR_FULL, 2) . str_repeat(self::STAR_EMPTY, 3);
        $this->assertEquals($expectedStars, $starString);
    }

    public function testThreeStarRating(): void
    {
        $starString = $this->generateStars(3.0);

        $expectedStars = str_repeat(self::STAR_FULL, 3) . str_repeat(self::STAR_EMPTY, 2);
        $this->assertEquals($expectedStars, $starString);
    }

    public function testFourStarRating(): void
    {
        $starString = $this->generateStars(4.0);

        $expectedStars = str_repeat(self::STAR_FULL, 4) . self::STAR_EMPTY;
        $this->assertEquals($expectedStars, $starString);
    }

    public function testFiveStarRating(): void
    {
        $starString = $this->generateStars(5.0);

        $expectedStars = str_repeat(self::STAR_FULL, 5);
        $this->assertEquals($expectedStars, $starString);
    }

    public function testHalfStarRatings(): void
    {
        // 0.5 should show half star + 4 empty
        $starString = $this->generateStars(0.5);
        $expectedStars = self::STAR_HALF . str_repeat(self::STAR_EMPTY, 4);
        $this->assertEquals($expectedStars, $starString);
    }

    public function testOneAndHalfStarRating(): void
    {
        $starString = $this->generateStars(1.5);

        $expectedStars = self::STAR_FULL . self::STAR_HALF . str_repeat(self::STAR_EMPTY, 3);
        $this->assertEquals($expectedStars, $starString);
    }

    public function testTwoAndHalfStarRating(): void
    {
        $starString = $this->generateStars(2.5);

        $expectedStars = str_repeat(self::STAR_FULL, 2) . self::STAR_HALF . str_repeat(self::STAR_EMPTY, 2);
        $this->assertEquals($expectedStars, $starString);
    }

    public function testThreeAndHalfStarRating(): void
    {
        $starString = $this->generateStars(3.5);

        $expectedStars = str_repeat(self::STAR_FULL, 3) . self::STAR_HALF . self::STAR_EMPTY;
        $this->assertEquals($expectedStars, $starString);
    }

    public function testFourAndHalfStarRating(): void
    {
        $starString = $this->generateStars(4.5);

        $expectedStars = str_repeat(self::STAR_FULL, 4) . self::STAR_HALF;
        $this->assertEquals($expectedStars, $starString);
    }

    public function testRatingTwoAndAThirdStars(): void
    {
        $starString = $this->generateStars(2.3333);

        $expectedStars = str_repeat(self::STAR_FULL, 2) . self::STAR_HALF . str_repeat(self::STAR_EMPTY, 2);
        $this->assertEquals($expectedStars, $starString);
    }

    public function testRatingTwoAndAQuarterStars(): void
    {
        $starString = $this->generateStars(2.25);

        $expectedStars = str_repeat(self::STAR_FULL, 2) . self::STAR_HALF . str_repeat(self::STAR_EMPTY, 2);
        $this->assertEquals($expectedStars, $starString);
    }

    public function testRatingFourPointNine(): void
    {
        $starString = $this->generateStars(4.9);

        $expectedStars = str_repeat(self::STAR_FULL, 4) . self::STAR_HALF;
        $this->assertEquals($expectedStars, $starString);
    }

    public function testRatingFourPointOne(): void
    {
        $starString = $this->generateStars(4.1);

        $expectedStars = str_repeat(self::STAR_FULL, 4) . self::STAR_HALF;
        $this->assertEquals($expectedStars, $starString);
    }

    #[DataProvider('starCountProvider')]
    public function testStarCountsAreCorrect(?float $rating, int $expectedFull, int $expectedHalf, int $expectedEmpty): void
    {
        $starString = $this->generateStars($rating);

        $fullCount = substr_count($starString, self::STAR_FULL);
        $halfCount = substr_count($starString, self::STAR_HALF);
        $emptyCount = substr_count($starString, self::STAR_EMPTY);

        $this->assertEquals($expectedFull, $fullCount, "Expected {$expectedFull} full stars");
        $this->assertEquals($expectedHalf, $halfCount, "Expected {$expectedHalf} half stars");
        $this->assertEquals($expectedEmpty, $emptyCount, "Expected {$expectedEmpty} empty stars");
        $this->assertEquals(5, $fullCount + $halfCount + $emptyCount, 'Total should be 5 stars');
    }

    public static function starCountProvider(): array
    {
        return [
            'null rating' => [null, 0, 0, 5],
            '0 stars' => [0.0, 0, 0, 5],
            '1 star' => [1.0, 1, 0, 4],
            '1.5 stars' => [1.5, 1, 1, 3],
            '2 stars' => [2.0, 2, 0, 3],
            '2.5 stars' => [2.5, 2, 1, 2],
            '3 stars' => [3.0, 3, 0, 2],
            '3.5 stars' => [3.5, 3, 1, 1],
            '4 stars' => [4.0, 4, 0, 1],
            '4.5 stars' => [4.5, 4, 1, 0],
            '5 stars' => [5.0, 5, 0, 0],
            '1.1 shows half' => [1.1, 1, 1, 3],
            '1.9 shows half' => [1.9, 1, 1, 3],
            '2.01 shows half' => [2.01, 2, 1, 2],
            '2.99 shows half' => [2.99, 2, 1, 2],
            '3.33 shows half' => [3.33, 3, 1, 1],
            '3.67 shows half' => [3.67, 3, 1, 1],
            '4.25 shows half' => [4.25, 4, 1, 0],
            '4.75 shows half' => [4.75, 4, 1, 0],
        ];
    }

    public function testGeneratorReturnsString(): void
    {
        $starString = $this->generateStars(3.0);

        $this->assertIsString($starString);
    }

    public function testGeneratorReturnsNonEmptyString(): void
    {
        $starString = $this->generateStars(3.0);

        $this->assertNotEmpty($starString);
    }

    public function testGeneratorContainsOnlyStarIcons(): void
    {
        $starString = $this->generateStars(3.5);

        // Remove all valid star icons
        $remaining = str_replace([self::STAR_FULL, self::STAR_HALF, self::STAR_EMPTY], '', $starString);

        $this->assertEmpty($remaining, 'String should only contain star icons');
    }

    public function testAlwaysReturnsFiveStars(): void
    {
        for ($rating = 0.0; $rating <= 5.0; $rating += 0.1) {
            $starString = $this->generateStars($rating);

            $fullCount = substr_count($starString, self::STAR_FULL);
            $halfCount = substr_count($starString, self::STAR_HALF);
            $emptyCount = substr_count($starString, self::STAR_EMPTY);

            $this->assertEquals(5, $fullCount + $halfCount + $emptyCount, "Rating {$rating} should produce exactly 5 stars");
        }
    }

    public function testHigherRatingMeansMoreFullStars(): void
    {
        $previousFullCount = 0;

        for ($rating = 1; $rating <= 5; $rating++) {
            $starString = $this->generateStars((float) $rating);
            $fullCount = substr_count($starString, self::STAR_FULL);

            $this->assertGreaterThanOrEqual($previousFullCount, $fullCount, "Rating {$rating} should have at least as many full stars as rating " . ($rating - 1));
            $previousFullCount = $fullCount;
        }
    }

    private function generateStars(?float $rating): string
    {
        $ride = new Ride();
        $ratingCalculator = $this->createMock(RatingCalculatorInterface::class);
        $ratingCalculator
            ->method('calculateRide')
            ->willReturn($rating);

        $starGenerator = new StarGenerator($ratingCalculator);

        return $starGenerator->generateForRide($ride);
    }
}
