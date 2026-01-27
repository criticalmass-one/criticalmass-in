<?php declare(strict_types=1);

namespace Tests\Controller;

class ProfileManagementControllerTest extends AbstractControllerTestCase
{
    public function testProfilePageAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/profile/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testProfilePageRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/profile/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testProfilePageContainsUsername(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $crawler = $client->request('GET', '/profile/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('testuser', $crawler->text());
    }

    public function testEditUsernameAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/profile/username');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEditEmailAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $client->request('GET', '/profile/email');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
