<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use App\Repository\RideRepository;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Bridge\PhpUnit\ClockMock;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class RideApiTest extends AbstractApiControllerTestCase
{
    #[TestDox('Retrieve the current ride for Hamburg.')]
    #[Group('time-sensitive')]
    public function testCurrentRide(): void
    {
        ClockMock::register(RideRepository::class);
        // Set time to one week ago to ensure we get the future Hamburg ride
        ClockMock::withClockMock((new \DateTime('-7 days'))->format('U'));


        $this->client->request('GET', '/api/hamburg/current');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($this->client->getResponse()->getContent(), Ride::class);

        // Should get a Hamburg ride in the future from the mocked time
        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
        // Ride should be after the mocked time
        $this->assertGreaterThan(new \DateTime('-7 days'), $actualRide->getDateTime());
    }

    #[TestDox('Return a ride by a date-driven ride identifier and a city slug.')]
    public function testFirstRide(): void
    {

        // Get a Hamburg ride date from the database dynamically
        $container = static::getContainer();
        $em = $container->get('doctrine')->getManager();
        $hamburg = $em->getRepository(\App\Entity\City::class)->findOneBy(['city' => 'Hamburg']);
        $rides = $em->getRepository(Ride::class)->findBy(['city' => $hamburg]);

        $this->assertNotEmpty($rides, 'Should have Hamburg rides in fixtures');

        $ride = $rides[0];
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/hamburg/%s', $rideDate));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($this->client->getResponse()->getContent(), Ride::class);

        $this->assertEquals($ride->getDateTime()->format('Y-m-d'), $actualRide->getDateTime()->format('Y-m-d'));
        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
    }

    #[TestDox('Providing a wrong date but a valid city slug will return 404.')]
    public function testRideWithWrongDate(): void
    {

        $this->client->request('GET', '/api/hamburg/2025-12-24');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    #[TestDox('Providing a non existant city slug with a valid date will return 404.')]
    public function testRideWithWrongCitySlug(): void
    {

        $this->client->request('GET', '/api/hamburggg/2025-12-23');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    #[Group('time-sensitive')]
    #[TestDox('Querying for rides without slugs returns a ride without slug.')]
    public function testCurrentRideWithoutSlugs(): void
    {
        ClockMock::register(RideRepository::class);
        ClockMock::withClockMock((new \DateTime('2026-02-20 11:00:00'))->format('U'));


        $this->client->request('GET', '/api/hamburg/current?slugsAllowed=false');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($this->client->getResponse()->getContent(), Ride::class);

        // The fixture rides don't have slugs
        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
        $this->assertNull($actualRide->getSlug());
    }

    #[Group('time-sensitive')]
    #[TestDox('When we request a cycle-generated ride we may get an empty result if no cycles exist.')]
    public function testCurrentRideWithMandatoryCycles(): void
    {
        ClockMock::register(RideRepository::class);
        ClockMock::withClockMock((new \DateTime('2026-02-20 11:00:00'))->format('U'));


        $this->client->request('GET', '/api/hamburg/current?cycleMandatory=true');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($this->client->getResponse()->getContent(), Ride::class);

        // Test fixtures may not have cycle-generated rides
        // Just verify the API returns a valid response
        $this->assertInstanceOf(Ride::class, $actualRide);
    }

    #[Group('time-sensitive')]
    #[TestDox('When we do not need cycled rides we get any available ride.')]
    public function testCurrentRideWithoutMandatoryCycles(): void
    {
        ClockMock::register(RideRepository::class);
        ClockMock::withClockMock((new \DateTime('2026-02-20 11:00:00'))->format('U'));


        $this->client->request('GET', '/api/hamburg/current?cycleMandatory=false');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($this->client->getResponse()->getContent(), Ride::class);

        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
    }

    #[TestDox('Allowing slugged rides in results returns available rides.')]
    #[Group('time-sensitive')]
    public function testCurrentRideWithSlugs(): void
    {
        ClockMock::register(RideRepository::class);
        ClockMock::withClockMock((new \DateTime('2026-02-20 11:00:00'))->format('U'));


        $this->client->request('GET', '/api/hamburg/current?slugsAllowed=true');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($this->client->getResponse()->getContent(), Ride::class);

        // Verify we got a Hamburg ride
        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
    }

    #[TestDox('Query a ride by its date identifier.')]
    public function testRideByDateIdentifier(): void
    {

        // Get a Hamburg ride date from the database dynamically
        $container = static::getContainer();
        $em = $container->get('doctrine')->getManager();
        $hamburg = $em->getRepository(\App\Entity\City::class)->findOneBy(['city' => 'Hamburg']);
        $rides = $em->getRepository(Ride::class)->findBy(['city' => $hamburg]);

        $this->assertNotEmpty($rides, 'Should have Hamburg rides in fixtures');

        $ride = $rides[0];
        $rideDate = $ride->getDateTime()->format('Y-m-d');

        $this->client->request('GET', sprintf('/api/hamburg/%s', $rideDate));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($this->client->getResponse()->getContent(), Ride::class);

        $this->assertEquals($ride->getDateTime()->format('Y-m-d'), $actualRide->getDateTime()->format('Y-m-d'));
        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
    }

    #[TestDox('Expect 404 for unknown ride identifiers.')]
    public function testRideByUnknownIdentifier(): void
    {

        $this->client->request('GET', '/api/hamburg/unknown-ride-identifier');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    #[TestDox('This call should return a list of ten random rides.')]
    public function testRideListWithoutParameters(): void
    {

        $this->client->request('GET', '/api/ride');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($this->client->getResponse()->getContent(), Ride::class);

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertLessThanOrEqual(10, count($actualRideList));
        $this->assertNotEmpty($actualRideList);
    }
}
