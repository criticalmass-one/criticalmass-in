<?php declare(strict_types=1);

namespace Tests\Controller;

class CityManagementControllerTest extends AbstractControllerTestCase
{
    public function testCityEditAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/hamburg/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCityEditRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/hamburg/edit');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testCityEditFormContainsCityName(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $crawler = $client->request('GET', '/hamburg/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorExists('input[name="city[city]"]');
        $cityField = $crawler->filter('input[name="city[city]"]');
        $this->assertEquals('Hamburg', $cityField->attr('value'));
    }

    public function testAddRideAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/hamburg/add-ride');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAddRideRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/hamburg/add-ride');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
