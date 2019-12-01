<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use Tests\Controller\Api\AbstractApiControllerTest;

class DateTimeTest extends AbstractApiControllerTest
{
    public function testRideListWithYear2050Parameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?year=2050');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 24.09.2050","description":null,"dateTime":2547658800,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }

    public function testRideListWithYear2016Month2Parameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?year=2016&month=2');

        $expectedContent = '[{"slug":null,"title":"Critical Mass London 01.02.2016","description":null,"dateTime":1454353200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Berlin 01.02.2016","description":null,"dateTime":1454353200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Esslingen 01.02.2016","description":null,"dateTime":1454353200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.02.2016","description":null,"dateTime":1454353200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Mainz 01.02.2016","description":null,"dateTime":1454353200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Halle 01.02.2016","description":null,"dateTime":1454353200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }

    public function testRideListWithYear2015Month6Day1Parameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?year=2015&month=6&day=1');

        $expectedContent = '[{"slug":null,"title":"Critical Mass London 01.06.2015","description":null,"dateTime":1433185200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Mainz 01.06.2015","description":null,"dateTime":1433185200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.06.2015","description":null,"dateTime":1433185200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Halle 01.06.2015","description":null,"dateTime":1433185200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Berlin 01.06.2015","description":null,"dateTime":1433185200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Esslingen 01.06.2015","description":null,"dateTime":1433185200,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }

    public function testRideListWithYear2011Month6Day24Parameter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?year=2011&month=6&day=24');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 24.06.2011","description":null,"dateTime":1308942000,"location":null,"latitude":53.5,"longitude":10.5,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }
}
