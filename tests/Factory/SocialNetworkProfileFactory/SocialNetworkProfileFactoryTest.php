<?php declare(strict_types=1);

namespace Tests\Factory\SocialNetworkProfileFactory;

use App\Entity\City;
use App\Entity\Ride;
use App\Entity\Subride;
use App\Entity\User;
use App\Factory\SocialNetworkProfile\SocialNetworkProfileFactory;
use PHPUnit\Framework\TestCase;

class SocialNetworkProfileFactoryTest extends TestCase
{
    public function testSocialNetworkProfileFactoryDefaultSocialNetworkProfile(): void
    {
        $socialNetworkProfileFactory = new SocialNetworkProfileFactory();
        $socialNetworkProfile = $socialNetworkProfileFactory->build();

        $this->assertEquals(true, $socialNetworkProfile->isEnabled());
        $this->assertEquals(false, $socialNetworkProfile->isMainNetwork());

        $this->assertEqualsWithDelta(new \DateTime(), $socialNetworkProfile->getCreatedAt(), 0.01);
        $this->assertNull($socialNetworkProfile->getCreatedBy());

        $this->assertNull($socialNetworkProfile->getCity());
        $this->assertNull($socialNetworkProfile->getRide());
        $this->assertNull($socialNetworkProfile->getSubride());
        $this->assertNull($socialNetworkProfile->getUser());
    }

    public function testSocialNetworkProfileFactoryWithCity(): void
    {
        $city = new City();

        $socialNetworkProfileFactory = new SocialNetworkProfileFactory();
        $socialNetworkProfile = $socialNetworkProfileFactory
            ->withCity($city)
            ->build();

        $this->assertEquals($city, $socialNetworkProfile->getCity());
        $this->assertNull($socialNetworkProfile->getRide());
        $this->assertNull($socialNetworkProfile->getSubride());
        $this->assertNull($socialNetworkProfile->getUser());
    }

    public function testSocialNetworkProfileFactoryWithRide(): void
    {
        $ride = new Ride();

        $socialNetworkProfileFactory = new SocialNetworkProfileFactory();
        $socialNetworkProfile = $socialNetworkProfileFactory
            ->withRide($ride)
            ->build();

        $this->assertNull($socialNetworkProfile->getCity());
        $this->assertEquals($ride, $socialNetworkProfile->getRide());
        $this->assertNull($socialNetworkProfile->getSubride());
        $this->assertNull($socialNetworkProfile->getUser());
    }

    public function testSocialNetworkProfileFactoryWithSubride(): void
    {
        $subride = new Subride();

        $socialNetworkProfileFactory = new SocialNetworkProfileFactory();
        $socialNetworkProfile = $socialNetworkProfileFactory
            ->withSubride($subride)
            ->build();

        $this->assertNull($socialNetworkProfile->getCity());
        $this->assertNull($socialNetworkProfile->getRide());
        $this->assertEquals($subride, $socialNetworkProfile->getSubride());
        $this->assertNull($socialNetworkProfile->getUser());
    }

    public function testSocialNetworkProfileFactoryWithUser(): void
    {
        $user = new User();

        $socialNetworkProfileFactory = new SocialNetworkProfileFactory();
        $socialNetworkProfile = $socialNetworkProfileFactory
            ->withUser($user)
            ->build();

        $this->assertNull($socialNetworkProfile->getCity());
        $this->assertNull($socialNetworkProfile->getRide());
        $this->assertNull($socialNetworkProfile->getSubride());
        $this->assertEquals($user, $socialNetworkProfile->getUser());
    }

    public function testSocialNetworkProfileFactoryWithEnabled(): void
    {
        $socialNetworkProfileFactory = new SocialNetworkProfileFactory();
        $socialNetworkProfile = $socialNetworkProfileFactory
            ->withEnabled(false)
            ->build();

        $this->assertFalse($socialNetworkProfile->isEnabled());
    }

    public function testSocialNetworkProfileFactoryWithMainNetwork(): void
    {
        $socialNetworkProfileFactory = new SocialNetworkProfileFactory();
        $socialNetworkProfile = $socialNetworkProfileFactory
            ->withMainNetwork(true)
            ->build();

        $this->assertTrue($socialNetworkProfile->isMainNetwork());
    }

    public function testSocialNetworkProfileFactoryWithUpdatedAt(): void
    {
        $user = new User();

        $socialNetworkProfileFactory = new SocialNetworkProfileFactory();
        $socialNetworkProfile = $socialNetworkProfileFactory
            ->withCreatedBy($user)
            ->build();

        $this->assertEquals($user, $socialNetworkProfile->getCreatedBy());
        $this->assertNull($socialNetworkProfile->getUser());
    }
}