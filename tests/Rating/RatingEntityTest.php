<?php declare(strict_types=1);

namespace Tests\Rating;

use App\Entity\Rating;
use App\Entity\Ride;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class RatingEntityTest extends TestCase
{
    public function testNewRatingHasNullId(): void
    {
        $rating = new Rating();

        $this->assertNull($rating->getId());
    }

    public function testNewRatingHasZeroStars(): void
    {
        $rating = new Rating();

        $this->assertEquals(0, $rating->getRating());
    }

    public function testNewRatingHasCreatedAtSet(): void
    {
        $rating = new Rating();

        $this->assertInstanceOf(\DateTime::class, $rating->getCreatedAt());
    }

    public function testCreatedAtIsRecentTimestamp(): void
    {
        $before = new \DateTime();
        $rating = new Rating();
        $after = new \DateTime();

        $this->assertGreaterThanOrEqual($before, $rating->getCreatedAt());
        $this->assertLessThanOrEqual($after, $rating->getCreatedAt());
    }

    public function testSetRating(): void
    {
        $rating = new Rating();
        $rating->setRating(4);

        $this->assertEquals(4, $rating->getRating());
    }

    public function testSetRatingReturnsself(): void
    {
        $rating = new Rating();
        $result = $rating->setRating(4);

        $this->assertSame($rating, $result);
    }

    public function testSetRide(): void
    {
        $rating = new Rating();
        $ride = new Ride();

        $rating->setRide($ride);

        $this->assertSame($ride, $rating->getRide());
    }

    public function testSetRideReturnsself(): void
    {
        $rating = new Rating();
        $ride = new Ride();

        $result = $rating->setRide($ride);

        $this->assertSame($rating, $result);
    }

    public function testSetRideToNull(): void
    {
        $rating = new Rating();
        $ride = new Ride();

        $rating->setRide($ride);
        $rating->setRide(null);

        $this->assertNull($rating->getRide());
    }

    public function testSetUser(): void
    {
        $rating = new Rating();
        $user = new User();

        $rating->setUser($user);

        $this->assertSame($user, $rating->getUser());
    }

    public function testSetUserReturnsself(): void
    {
        $rating = new Rating();
        $user = new User();

        $result = $rating->setUser($user);

        $this->assertSame($rating, $result);
    }

    public function testSetUserToNull(): void
    {
        $rating = new Rating();
        $user = new User();

        $rating->setUser($user);
        $rating->setUser(null);

        $this->assertNull($rating->getUser());
    }

    public function testSetCreatedAt(): void
    {
        $rating = new Rating();
        $dateTime = new \DateTime('2024-01-15 12:00:00');

        $rating->setCreatedAt($dateTime);

        $this->assertSame($dateTime, $rating->getCreatedAt());
    }

    public function testSetCreatedAtReturnsself(): void
    {
        $rating = new Rating();
        $dateTime = new \DateTime();

        $result = $rating->setCreatedAt($dateTime);

        $this->assertSame($rating, $result);
    }

    public function testFluentInterface(): void
    {
        $rating = new Rating();
        $ride = new Ride();
        $user = new User();
        $dateTime = new \DateTime();

        $result = $rating
            ->setRating(5)
            ->setRide($ride)
            ->setUser($user)
            ->setCreatedAt($dateTime);

        $this->assertSame($rating, $result);
        $this->assertEquals(5, $rating->getRating());
        $this->assertSame($ride, $rating->getRide());
        $this->assertSame($user, $rating->getUser());
        $this->assertSame($dateTime, $rating->getCreatedAt());
    }

    public function testSetMinimumRating(): void
    {
        $rating = new Rating();
        $rating->setRating(1);

        $this->assertEquals(1, $rating->getRating());
    }

    public function testSetMaximumRating(): void
    {
        $rating = new Rating();
        $rating->setRating(5);

        $this->assertEquals(5, $rating->getRating());
    }

    public function testNewRatingHasNullRide(): void
    {
        $rating = new Rating();

        $this->assertNull($rating->getRide());
    }

    public function testNewRatingHasNullUser(): void
    {
        $rating = new Rating();

        $this->assertNull($rating->getUser());
    }
}
