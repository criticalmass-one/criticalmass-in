<?php declare(strict_types=1);

namespace Tests\Controller\Ride;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DisableRideTest extends WebTestCase
{
    public function testEnabledRide(): void
    {
        $client = static::createClient();

        $client->request('GET', '/hamburg/2011-06-24');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1', 'Critical Mass 24.06.2011');
    }
}
