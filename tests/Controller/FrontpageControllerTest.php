<?php declare(strict_types=1);

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontpageControllerTest extends WebTestCase
{
    public function testFrontpageVisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1', 'Hej, wir fahren Fahrrad!');
    }

    public function testMandatoryFrontpageFooterLinks(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertSelectorTextContains('#footer a#footer-impress-link', 'Impressum');
        $this->assertSelectorTextContains('#footer a#footer-privacy-link', 'Datenschutz');
        $this->assertSelectorTextContains('#footer a#footer-gdpr-link', 'Deine privaten Daten');
        $this->assertSelectorTextContains('#footer a#footer-about-link', 'Über die Critical Mass');
        $this->assertSelectorTextContains('#footer a#footer-faq-link', 'Häufig gestellte Fragen');
        $this->assertSelectorTextContains('#footer a#footer-citylist-link', 'Städteliste');
        $this->assertSelectorTextContains('#footer a#footer-status-link', 'Status');
    }
}