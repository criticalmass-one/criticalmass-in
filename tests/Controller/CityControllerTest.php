<?php declare(strict_types=1);

namespace Tests\Controller;

class CityControllerTest extends AbstractControllerTestCase
{
    public function testCityPageHamburg(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hamburg');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html', 'Hamburg');
    }

    public function testCityPageBerlin(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/berlin');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html', 'Berlin');
    }

    public function testCityPageMunich(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/munich');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html', 'Munich');
    }

    public function testCityPageKiel(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/kiel');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html', 'Kiel');
    }

    public function testNonExistentCityReturnsNoServerError(): void
    {
        $client = static::createClient();

        $client->request('GET', '/nonexistent-city-xyz');

        $this->assertLessThan(500, $client->getResponse()->getStatusCode());
    }

    public function testRideListPage(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hamburg/list');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGalleryListPage(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hamburg/galleries');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCityListPage(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/citylist');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html', 'Hamburg');
        $this->assertSelectorTextContains('html', 'Berlin');
    }

    public function testCityPageContainsNavigationTabs(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hamburg');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $rideListLink = $crawler->filter('a[href="/hamburg/list"]');
        $this->assertGreaterThan(0, $rideListLink->count(), 'City page should contain a link to the ride list');

        $galleryLink = $crawler->filter('a[href="/hamburg/galleries"]');
        $this->assertGreaterThan(0, $galleryLink->count(), 'City page should contain a link to galleries');
    }
}
