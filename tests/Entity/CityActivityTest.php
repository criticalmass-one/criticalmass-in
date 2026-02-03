<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\City;
use App\Entity\CityActivity;
use PHPUnit\Framework\TestCase;

class CityActivityTest extends TestCase
{
    public function testCityActivityCreation(): void
    {
        $city = $this->createMock(City::class);

        $activity = new CityActivity();
        $activity->setCity($city);
        $activity->setScore(0.85);
        $activity->setParticipationScore(0.80);
        $activity->setParticipationRawCount(42);
        $activity->setPhotoScore(0.90);
        $activity->setPhotoRawCount(51);
        $activity->setTrackScore(0.75);
        $activity->setTrackRawCount(9);
        $activity->setSocialFeedScore(0.85);
        $activity->setSocialFeedRawCount(30);

        $this->assertSame($city, $activity->getCity());
        $this->assertEquals(0.85, $activity->getScore());
        $this->assertEquals(0.80, $activity->getParticipationScore());
        $this->assertEquals(42, $activity->getParticipationRawCount());
        $this->assertEquals(0.90, $activity->getPhotoScore());
        $this->assertEquals(51, $activity->getPhotoRawCount());
        $this->assertEquals(0.75, $activity->getTrackScore());
        $this->assertEquals(9, $activity->getTrackRawCount());
        $this->assertEquals(0.85, $activity->getSocialFeedScore());
        $this->assertEquals(30, $activity->getSocialFeedRawCount());
    }

    public function testCreatedAtIsSetAutomatically(): void
    {
        $activity = new CityActivity();

        $this->assertInstanceOf(\DateTimeImmutable::class, $activity->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $activity = new CityActivity();
        $customDate = new \DateTimeImmutable('2026-01-15 10:30:00');

        $activity->setCreatedAt($customDate);

        $this->assertEquals($customDate, $activity->getCreatedAt());
    }

    public function testFluentInterface(): void
    {
        $city = $this->createMock(City::class);
        $activity = new CityActivity();

        $result = $activity
            ->setCity($city)
            ->setScore(0.5)
            ->setParticipationScore(0.5)
            ->setParticipationRawCount(10)
            ->setPhotoScore(0.5)
            ->setPhotoRawCount(10)
            ->setTrackScore(0.5)
            ->setTrackRawCount(10)
            ->setSocialFeedScore(0.5)
            ->setSocialFeedRawCount(10);

        $this->assertSame($activity, $result);
    }
}
