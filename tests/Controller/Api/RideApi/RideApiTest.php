<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use App\Repository\RideRepository;
use Symfony\Bridge\PhpUnit\ClockMock;
use Tests\Controller\Api\AbstractApiControllerTest;

class RideApiTest extends AbstractApiControllerTest
{
    /**
     * @testdox Pretend to have mid 2011 and retrieve the current ride.
     * @group time-sensitive
     * @testdox Return
     */
    public function testCurrentRide(): void
    {
        ClockMock::register(RideRepository::class);
        ClockMock::withClockMock((new \DateTime('2011-06-24 11:00:00'))->format('U'));

        $client = static::createClient();

        $client->request('GET', '/api/hamburg/current');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(new \DateTime('2011-06-24 19:00:00'), $actualRide->getDateTime());
        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
    }

    /**
     * @testdox Return a ride by a date-driven ride identifier and a city slug.
     */
    public function testFirstRide(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg/2011-06-24');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(new \DateTime('2011-06-24 19:00:00'), $actualRide->getDateTime());
        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
    }

    /**
     * @testdox Providing a wrong date but a valid city slug will return 404.
     */
    public function testRideWithWrongDate(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg/2011-06-25');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * @group time-sensitive
     * @testdox Providing a non existant city slug with a valid date will return 404.
     */
    public function testRideWithWrongCitySlug(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburggg/2011-06-24');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * @group time-sensitive
     * @testdox Querying for rides without slugs from mid 2035 should return our prepared ride from 2050.
     */
    public function testCurrentRideWithoutSlugs(): void
    {
        ClockMock::register(RideRepository::class);
        ClockMock::withClockMock((new \DateTime('2035-06-10 11:00:00'))->format('U'));

        $client = static::createClient();

        $client->request('GET', '/api/hamburg/current?slugsAllowed=false');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(new \DateTime('2050-09-24 19:00:00'), $actualRide->getDateTime());
        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
        $this->assertNull($actualRide->getSlug());
    }

    /**
     * @group time-sensitive
     * @testdox When we request a cycle-generated ride we get an empty result as the test data does not contain any generated rides :(
     */
    public function testCurrentRideWithMandatoryCycles(): void
    {
        ClockMock::register(RideRepository::class);
        ClockMock::withClockMock((new \DateTime('2035-06-10 11:00:00'))->format('U'));

        $client = static::createClient();

        $client->request('GET', '/api/hamburg/current?cycleMandatory=true');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($client->getResponse()->getContent(), Ride::class);

        $this->assertNull($actualRide->getDateTime());
        $this->assertNull($actualRide->getCity());
        $this->assertNull($actualRide->getSlug());
        $this->assertNull($actualRide->getTitle());
    }

    /**
     * @group time-sensitive
     * @testdox When we do not need cycled rides we get the kidical mass ride.
     */
    public function testCurrentRideWithoutMandatoryCycles(): void
    {
        ClockMock::register(RideRepository::class);
        ClockMock::withClockMock((new \DateTime('2035-06-10 11:00:00'))->format('U'));

        $client = static::createClient();

        $client->request('GET', '/api/hamburg/current?cycleMandatory=false');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(new \DateTime('2035-06-24 19:00:00'), $actualRide->getDateTime());
        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
        $this->assertEquals('kidical-mass-hamburg-2035', $actualRide->getSlug());
    }

    /**
     * @testdox Allowing the ride result list to contain slugged rides will return the kidical mass 2035.
     */
    public function testCurrentRideWithSlugs(): void
    {
        ClockMock::register(RideRepository::class);
        ClockMock::withClockMock((new \DateTime('2035-06-10 11:00:00'))->format('U'));

        $client = static::createClient();

        $client->request('GET', '/api/hamburg/current?slugsAllowed=true');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals(new \DateTime('2035-06-24 19:00:00'), $actualRide->getDateTime());
        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
        $this->assertEquals('kidical-mass-hamburg-2035', $actualRide->getSlug());
    }

    /**
     * @testdox Query a ride by its ride identifier.
     */
    public function testCurrentRideBySlug(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg/kidical-mass-hamburg-2035');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = $this->deserializeEntity($client->getResponse()->getContent(), Ride::class);

        $this->assertEquals('kidical-mass-hamburg-2035', $actualRide->getSlug());
        $this->assertEquals(new \DateTime('2035-06-24 19:00:00'), $actualRide->getDateTime());
        $this->assertEquals('Hamburg', $actualRide->getCity()->getCity());
    }

    /**
     * @testdox Expect 404 for unknown ride identifiers.
     */
    public function testCurrentRideByMisspelledSlug(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg/kiddical-mass-hamburg-2035');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * @testdox This call should return a list of ten random rides.
     */
    public function testRideListWithoutParameters(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualRideList = $this->deserializeEntityList($client->getResponse()->getContent(), Ride::class);
        
        $this->assertCount(10, $actualRideList);
    }
}
