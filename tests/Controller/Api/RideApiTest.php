<?php declare(strict_types=1);

namespace Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RideApiTest extends WebTestCase
{
    public function testCurrentRide(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg/current');

        $expectedContent = '{"id":8,"cycle":null,"city":{"slug":"hamburg","id":1,"mainSlug":{"id":1,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":1,"slug":"hamburg"}],"cityPopulation":0,"punchLine":null,"longDescription":null,"timezone":"Europe\/Berlin","threadNumber":0,"postNumber":0,"colorRed":0,"colorGreen":0,"colorBlue":0},"slug":null,"title":"Critical Mass 24.09.2050","description":null,"dateTime":2547658800,"location":null,"latitude":null,"longitude":null,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null,"facebook":null,"twitter":null,"url":null,"participationsNumberYes":0,"participationsNumberMaybe":0,"participationsNumberNo":0}';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expectedContent, $client->getResponse()->getContent());
    }

    public function testFirstRide(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg/2011-06-24');

        $expectedContent = '{"id":1,"cycle":null,"city":{"slug":"hamburg","id":1,"mainSlug":{"id":1,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":1,"slug":"hamburg"}],"cityPopulation":0,"punchLine":null,"longDescription":null,"timezone":"Europe\/Berlin","threadNumber":0,"postNumber":0,"colorRed":0,"colorGreen":0,"colorBlue":0},"slug":null,"title":"Critical Mass 24.06.2011","description":null,"dateTime":1308942000,"location":null,"latitude":null,"longitude":null,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null,"facebook":null,"twitter":null,"url":null,"participationsNumberYes":0,"participationsNumberMaybe":0,"participationsNumberNo":0}';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expectedContent, $client->getResponse()->getContent());
    }

    public function testRideList(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride');

        $expectedContent = '[{"id":8,"cycle":null,"city":{"id":1,"mainSlug":{"id":1,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":1,"slug":"hamburg"}],"timezone":"Europe\/Berlin"},"slug":null,"title":"Critical Mass 24.09.2050","description":null,"dateTime":2547658800,"location":null,"latitude":null,"longitude":null,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null,"facebook":null,"twitter":null,"url":null},{"id":7,"cycle":null,"city":{"id":1,"mainSlug":{"id":1,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":1,"slug":"hamburg"}],"timezone":"Europe\/Berlin"},"slug":null,"title":"Critical Mass 30.06.2011","description":null,"dateTime":1309460400,"location":null,"latitude":null,"longitude":null,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null,"facebook":null,"twitter":null,"url":null},{"id":6,"cycle":null,"city":{"id":1,"mainSlug":{"id":1,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":1,"slug":"hamburg"}],"timezone":"Europe\/Berlin"},"slug":null,"title":"Critical Mass 29.06.2011","description":null,"dateTime":1309374000,"location":null,"latitude":null,"longitude":null,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null,"facebook":null,"twitter":null,"url":null},{"id":5,"cycle":null,"city":{"id":1,"mainSlug":{"id":1,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":1,"slug":"hamburg"}],"timezone":"Europe\/Berlin"},"slug":null,"title":"Critical Mass 28.06.2011","description":null,"dateTime":1309287600,"location":null,"latitude":null,"longitude":null,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null,"facebook":null,"twitter":null,"url":null},{"id":4,"cycle":null,"city":{"id":1,"mainSlug":{"id":1,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":1,"slug":"hamburg"}],"timezone":"Europe\/Berlin"},"slug":null,"title":"Critical Mass 27.06.2011","description":null,"dateTime":1309201200,"location":null,"latitude":null,"longitude":null,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null,"facebook":null,"twitter":null,"url":null},{"id":3,"cycle":null,"city":{"id":1,"mainSlug":{"id":1,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":1,"slug":"hamburg"}],"timezone":"Europe\/Berlin"},"slug":null,"title":"Critical Mass 26.06.2011","description":null,"dateTime":1309114800,"location":null,"latitude":null,"longitude":null,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null,"facebook":null,"twitter":null,"url":null},{"id":2,"cycle":null,"city":{"id":1,"mainSlug":{"id":1,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":1,"slug":"hamburg"}],"timezone":"Europe\/Berlin"},"slug":null,"title":"Critical Mass 25.06.2011","description":null,"dateTime":1309028400,"location":null,"latitude":null,"longitude":null,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null,"facebook":null,"twitter":null,"url":null},{"id":1,"cycle":null,"city":{"id":1,"mainSlug":{"id":1,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":1,"slug":"hamburg"}],"timezone":"Europe\/Berlin"},"slug":null,"title":"Critical Mass 24.06.2011","description":null,"dateTime":1308942000,"location":null,"latitude":null,"longitude":null,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null,"facebook":null,"twitter":null,"url":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expectedContent, $client->getResponse()->getContent());
    }
}
