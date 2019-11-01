<?php declare(strict_types=1);

namespace Tests\Rating;

use App\Criticalmass\Rating\Calculator\RatingCalculatorInterface;
use App\Criticalmass\Rating\StarGenerator\StarGenerator;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class StarGeneratorTest extends TestCase
{
    public function testNull(): void
    {
        $ride = new Ride();
        $ratingCalculator = $this->createMock(RatingCalculatorInterface::class);
        $ratingCalculator
            ->method('calculateRide')
            ->will($this->returnValue(null));

        $starGenerator = new StarGenerator($ratingCalculator);

        $starString = $starGenerator->generateForRide($ride);

        $this->assertEquals('<i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>', $starString);
    }

    public function testRatingTwoStars(): void
    {
        $ride = new Ride();
        $ratingCalculator = $this->createMock(RatingCalculatorInterface::class);
        $ratingCalculator
            ->method('calculateRide')
            ->will($this->returnValue(2));

        $starGenerator = new StarGenerator($ratingCalculator);

        $starString = $starGenerator->generateForRide($ride);

        $this->assertEquals('<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>', $starString);
    }

    public function testRatingFiveStars(): void
    {
        $ride = new Ride();
        $ratingCalculator = $this->createMock(RatingCalculatorInterface::class);
        $ratingCalculator
            ->method('calculateRide')
            ->will($this->returnValue(5));

        $starGenerator = new StarGenerator($ratingCalculator);

        $starString = $starGenerator->generateForRide($ride);

        $this->assertEquals('<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>', $starString);
    }
}
