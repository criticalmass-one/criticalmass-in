<?php declare(strict_types=1);

namespace Tests\Controller\Ride;

use Tests\Controller\AbstractControllerTest;

class DisableRideTest extends AbstractControllerTest
{
    public function testEnabledRide(): void
    {
        $client = static::createClient();

        $client->request('GET', '/hamburg/2011-06-24');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1', 'Critical Mass 24.06.2011');
        $this->assertSelectorExists('body.ride');
        $this->assertSelectorNotExists('body.ride-disabled');
    }

    public function testDisableRideFormVisibility(): void
    {
        $client = static::createClient();

        $client->request('GET', '/hamburg/2011-06-24');

        $this->assertSelectorExists('body.not-logged-in');
        $this->assertSelectorNotExists('#disable-modal');

        $client = $this->loginViaForm($client, 'maltehuebner', '123456');

        $client->request('GET', '/hamburg/2011-06-24');

        $this->assertSelectorExists('body.logged-in');
        $this->assertSelectorExists('#disable-modal');
    }

    /**
     * @depends testEnabledRide
     * @depends testDisableRideFormVisibility
     */
    public function testDisableRide(): void
    {
        $client = static::createClient();

        $client = $this->loginViaForm($client, 'maltehuebner', '123456');

        $crawler = $client->request('GET', '/hamburg/2011-06-24');

        $this->assertSelectorExists('body.logged-in');

        $form = $crawler->filter('#disable-modal form')->form();

        $form['ride_disable[disabledReason]']->select('DUPLICATE');

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1', 'Critical Mass 24.06.2011');
        $this->assertSelectorExists('body.ride');
        $this->assertSelectorExists('body.ride-disabled');
    }

    /**
     * @depends testDisableRide
     */
    public function testNavigationWithoutDisabledRide(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hamburg/2011-03-25');

        $nextLink = $crawler->filter('.pager .next a')->link();

        $this->assertEquals('http://localhost/hamburg/2011-07-29', $nextLink->getUri());

        $crawler = $client->request('GET', '/hamburg/2011-07-29');

        $prevLink = $crawler->filter('.pager .previous a')->link();

        $this->assertEquals('http://localhost/hamburg/2011-03-25', $prevLink->getUri());
    }

    /**
     * @depends testDisableRide
     */
    public function testEnableRide(): void
    {
        $client = static::createClient();

        $client = $this->loginViaForm($client, 'maltehuebner', '123456');

        $crawler = $client->request('GET', '/hamburg/2011-06-24/edit');

        $form = $crawler->filter('form[name="ride"]')->form();

        $form['ride[enabled]']->tick();

        $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/hamburg/2011-06-24');

        $this->assertSelectorTextContains('html h1', 'Critical Mass 24.06.2011');
        $this->assertSelectorExists('body.ride');
        $this->assertSelectorNotExists('body.ride-disabled');
    }
}
