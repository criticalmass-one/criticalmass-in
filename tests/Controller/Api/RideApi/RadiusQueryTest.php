<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use Tests\Controller\Api\AbstractApiControllerTest;

class RadiusQueryTest extends AbstractApiControllerTest
{
    public function testRideListWithRadiusQuery(): void
    {
        $this->markTestSkipped('This is still broken.');

        $client = static::createClient();

        $client->request('GET', '/api/ride?centerLatitude=54.343024&centerLongitude=10.129730&radius=10');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 24.09.2050","description":null,"dateTime":2547658800,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":"kidical-mass-hamburg-2035","title":"Critical Mass Hamburg 24.06.2035","description":null,"dateTime":2066324400,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Mainz 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Berlin 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Esslingen 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Halle 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass London 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass London 01.11.2029","description":null,"dateTime":1888254000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Esslingen 01.11.2029","description":null,"dateTime":1888254000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }
}
