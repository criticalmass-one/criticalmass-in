<?php declare(strict_types=1);

namespace Tests\Controller;

class NavigationTest extends AbstractControllerTestCase
{
    public function testFooterContainsMandatoryLinks(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hamburg');

        $this->assertGreaterThan(0, $crawler->filter('#footer a#footer-impress-link')->count(), 'Footer should contain impress link');
        $this->assertGreaterThan(0, $crawler->filter('#footer a#footer-privacy-link')->count(), 'Footer should contain privacy link');
    }

    public function testCityPageContainsRideLink(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hamburg');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $rideLinks = $crawler->filter('a[href]')->reduce(function ($node) {
            return (bool) preg_match('#^/hamburg/\d{4}-\d{2}-\d{2}$#', $node->attr('href'));
        });

        $this->assertGreaterThan(0, $rideLinks->count(), 'City page should contain a link to a ride');
    }

    public function testCityListContainsFixtureCities(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/citylist');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $pageContent = $crawler->filter('html')->text();

        $this->assertStringContainsString('Hamburg', $pageContent);
        $this->assertStringContainsString('Berlin', $pageContent);
        $this->assertStringContainsString('Munich', $pageContent);
        $this->assertStringContainsString('Kiel', $pageContent);
    }
}
