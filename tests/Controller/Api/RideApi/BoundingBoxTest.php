<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;

class BoundingBoxTest extends AbstractApiControllerTest
{
    /**
     * @testdox This will test for any rides in a bounding box in Hamburg from Hamburg-Eidelstedt (northwest) to Hamburg-Hamm (southeast).
     */
    public function testRideListWithBoundingBoxQueryForHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?bbNorthLatitude=53.606153&bbWestLongitude=9.905992&bbSouthLatitude=53.547299&bbEastLongitude=10.054452');

        $actualRideList = ($this->deserializeEntityList($client->getResponse()->getContent(), Ride::class));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $ride */
        foreach ($actualRideList as $ride) {
            $this->assertEquals(53.566676, $ride->getLatitude());
            $this->assertEquals(9.984711, $ride->getLongitude());
        }
    }

    public function testRideListWithInvalidBoundingBoxQuery(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?bbNorthLatitude=54&bbSouthLatitude=57&bbEastLongitude=9&bbWestLongitude=10.054470');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 01.03.2015","description":null,"dateTime":1425236400,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.04.2015","description":null,"dateTime":1427914800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.05.2015","description":null,"dateTime":1430506800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.10.2015","description":null,"dateTime":1443726000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.12.2015","description":null,"dateTime":1448996400,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.05.2016","description":null,"dateTime":1462129200,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.07.2017","description":null,"dateTime":1498935600,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.11.2018","description":null,"dateTime":1541098800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.07.2019","description":null,"dateTime":1562007600,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.09.2019","description":null,"dateTime":1567364400,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }

    /**
     * @testdox This will test for rides in a bounding box in Hamburg from Hamburg-Eidelstedt (northwest) to Hamburg-Hamm (southeast) on 2011-06-24.
     */
    public function testRideListWithBoundingBoxForHamburgAndYearMonthDayQuery(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?year=2011&month=6&day=24&bbNorthLatitude=53.606120&bbSouthLatitude=53.547127&bbWestLongitude=9.906029&bbEastLongitude=10.054470');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 24.06.2011","description":null,"dateTime":1308942000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $actualRideList = ($this->deserializeEntityList($client->getResponse()->getContent(), Ride::class));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $actualRide */
        $actualRide = array_pop($actualRideList);

        $this->assertEquals(53.566676, $actualRide->getLatitude());
        $this->assertEquals(9.984711, $actualRide->getLongitude());
        $this->assertEquals(new \DateTime('2011-06-24 19:00:00'), $actualRide->getDateTime());
    }

    /**
     * @testdox This will test for rides in a bounding box in Hamburg from Hamburg-Eidelstedt (northwest) to Hamburg-Hamm (southeast) on 2011-06-24.
     */
    public function testRideListWithBoundingBoxQueryForLondon(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?bbNorthLatitude=51.527641&bbSouthLatitude=51.503026&bbWestLongitude=-0.153760&bbEastLongitude=0.003207');

        $actualRideList = ($this->deserializeEntityList($client->getResponse()->getContent(), Ride::class));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var Ride $ride */
        foreach ($actualRideList as $ride) {
            $this->assertEquals(51.50762, $ride->getLatitude());
            $this->assertEquals(-0.114708, $ride->getLongitude());
        }
    }
}
