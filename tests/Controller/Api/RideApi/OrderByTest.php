<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use Tests\Controller\Api\AbstractApiControllerTest;

class OrderByTest extends AbstractApiControllerTest
{
    public function testRideListOrderByDateTimeAscending(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?orderBy=dateTime&orderDirection=ASC');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 25.03.2011","description":null,"dateTime":1301079600,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 24.06.2011","description":null,"dateTime":1308942000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 29.07.2011","description":null,"dateTime":1311966000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Mainz 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Esslingen 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Halle 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass London 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Berlin 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.02.2015","description":null,"dateTime":1422817200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }

    public function testRideListOrderByDateTimeDescending(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?orderBy=dateTime&orderDirection=DESC');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 24.09.2050","description":null,"dateTime":2547658800,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":"kidical-mass-hamburg-2035","title":"Critical Mass Hamburg 24.06.2035","description":null,"dateTime":2066324400,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Mainz 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Berlin 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Esslingen 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Halle 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass London 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass London 01.11.2029","description":null,"dateTime":1888254000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Esslingen 01.11.2029","description":null,"dateTime":1888254000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }
}
