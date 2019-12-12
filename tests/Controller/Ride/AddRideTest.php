<?php declare(strict_types=1);

namespace Tests\Controller\Ride;

use Tests\Controller\AbstractControllerTest;

class AddRideTest extends AbstractControllerTest
{
    public function testAddRideIsNotAccessableWithoutLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/hamburg/add-ride');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAddRideIsAccessableWithLogin(): void
    {
        $client = static::createClient();

        $this->loginViaForm($client, 'maltehuebner', '123456');

        $client->request('GET', '/hamburg/add-ride');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAddRidePage(): void
    {
        $client = static::createClient();

        $this->loginViaForm($client, 'maltehuebner', '123456');

        $client->request('GET', '/hamburg/add-ride');

        $this->assertSelectorExists('#map');
        $this->assertSelectorExists('input#ride_title');
        $this->assertSelectorExists('textarea#ride_description');
        $this->assertSelectorExists('input#ride_dateTime_date');
        $this->assertSelectorExists('input#ride_dateTime_time');
        $this->assertSelectorExists('input#ride_location');
        $this->assertSelectorExists('input#ride_latitude');
        $this->assertSelectorExists('input#ride_longitude');
        $this->assertSelectorExists('input#ride__token');
    }
}
