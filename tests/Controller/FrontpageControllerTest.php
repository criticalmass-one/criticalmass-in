<?php declare(strict_types=1);

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontpageControllerTest extends WebTestCase
{
    public function testFrontpageVisible(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('h1')->count(), 'Frontpage should contain h1 heading');
    }

    public function testMandatoryFrontpageFooterLinks(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertGreaterThan(0, $crawler->filter('#footer a#footer-impress-link')->count(), 'Footer should contain impress link');
        $this->assertGreaterThan(0, $crawler->filter('#footer a#footer-privacy-link')->count(), 'Footer should contain privacy link');
        $this->assertGreaterThan(0, $crawler->filter('#footer a#footer-gdpr-link')->count(), 'Footer should contain GDPR link');
        $this->assertGreaterThan(0, $crawler->filter('#footer a#footer-about-link')->count(), 'Footer should contain about link');
        $this->assertGreaterThan(0, $crawler->filter('#footer a#footer-faq-link')->count(), 'Footer should contain FAQ link');
        $this->assertGreaterThan(0, $crawler->filter('#footer a#footer-citylist-link')->count(), 'Footer should contain city list link');
        $this->assertGreaterThan(0, $crawler->filter('#footer a#footer-status-link')->count(), 'Footer should contain status link');
    }
}
