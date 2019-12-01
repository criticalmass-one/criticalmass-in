<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use Tests\Controller\Api\AbstractApiControllerTest;

class BoundingBoxTest extends AbstractApiControllerTest
{
    /**
     * @testdox This will test for any rides in a bounding box in Hamburg from Hamburg-Eidelstedt (northwest) to Hamburg-Hamm (southeast).
     */
    public function testRideListWithBoundingBoxQuery(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?bbNorthLatitude=53.606120&bbSouthLatitude=53.547127&bbWestLongitude=9.906029&bbRightLongitude=10.054470');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 01.02.2015","description":null,"dateTime":1422817200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.12.2015","description":null,"dateTime":1448996400,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.04.2016","description":null,"dateTime":1459537200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.05.2016","description":null,"dateTime":1462129200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.08.2016","description":null,"dateTime":1470078000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.01.2017","description":null,"dateTime":1483297200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.10.2017","description":null,"dateTime":1506884400,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.12.2017","description":null,"dateTime":1512154800,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.01.2018","description":null,"dateTime":1514833200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.03.2018","description":null,"dateTime":1519930800,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }

    /**
     * @testdox This will test for rides in a bounding box in Hamburg from Hamburg-Eidelstedt (northwest) to Hamburg-Hamm (southeast) on 2011-06-24.
     */
    public function testRideListWithBoundingBoxAndYearMonthDayQuery(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?year=2011&month=6&day=24&bbNorthLatitude=53.606120&bbSouthLatitude=53.547127&bbWestLongitude=9.906029&bbRightLongitude=10.054470');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 24.06.2011","description":null,"dateTime":1308942000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }
}
