<?php declare(strict_types=1);

namespace Tests\Controller\Security;

use App\Entity\User;
use Tests\Controller\AbstractControllerTest;

class SecurityControllerTest extends AbstractControllerTest
{
    public function testLoginPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login/');

        $this->assertEquals(301, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorExists('body.not-logged-in');
        $this->assertSelectorTextContains('html h1', 'Einloggen und losfahren!');
        $this->assertSelectorExists('input[name=_username]');
        $this->assertSelectorExists('input[name=_password]');
    }

    /**
     * @depends testLoginPage
     */
    public function testLoginWithRightCredentials(): void
    {
        $testUser = $this->createTestUser();

        $client = static::createClient();

        $client->request('GET', '/login/');

        $crawler = $client->followRedirect();

        $this->assertSelectorExists('body.not-logged-in');

        $form = $crawler->filter('.form-horizontal')->form();

        $form->setValues([
            '_username' => $testUser->getUsername(),
            '_password' => 'test-123456',
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorExists('body.logged-in');
    }

    /**
     * @depends testLoginPage
     */
    public function testLoginWithWrongUsername(): void
    {
        $testUser = $this->createTestUser();

        $client = static::createClient();

        $client->request('GET', '/login/');

        $crawler = $client->followRedirect();

        $this->assertSelectorExists('body.not-logged-in');

        $form = $crawler->filter('.form-horizontal')->form();

        $form->setValues([
            '_username' => sprintf('%s%s', $testUser->getUsername(), '-foo'),
            '_password' => 'test-123456',
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorExists('body.not-logged-in');
    }

    /**
     * @depends testLoginPage
     */
    public function testLoginWithWrongPassword(): void
    {
        $testUser = $this->createTestUser();

        $client = static::createClient();

        $client->request('GET', '/login/');

        $crawler = $client->followRedirect();

        $this->assertSelectorExists('body.not-logged-in');

        $form = $crawler->filter('.form-horizontal')->form();

        $form->setValues([
            '_username' => $testUser->getUsername(),
            '_password' => 'test-123456-foo',
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorExists('body.not-logged-in');
    }

    /**
     * @depends testLoginPage
     */
    public function testLoginWithWrongCredentials(): void
    {
        $testUser = $this->createTestUser();

        $client = static::createClient();

        $client->request('GET', '/login/');

        $crawler = $client->followRedirect();

        $this->assertSelectorExists('body.not-logged-in');

        $form = $crawler->filter('.form-horizontal')->form();

        $form->setValues([
            '_username' => sprintf('%s%s', $testUser->getUsername(), '-foo'),
            '_password' => 'test-123456-foo',
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorExists('body.not-logged-in');
    }

    /**
     * @depends testLoginPage
     */
    public function testLoginWithDisabledUser(): void
    {
        $testUser = $this->createTestUser(false);

        $client = static::createClient();

        $client->request('GET', '/login/');

        $crawler = $client->followRedirect();

        $this->assertSelectorExists('body.not-logged-in');

        $form = $crawler->filter('.form-horizontal')->form();

        $form->setValues([
            '_username' => $testUser->getUsername(),
            '_password' => 'test-123456',
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorExists('body.not-logged-in');
    }

    /**
     * @depends testLoginWithRightCredentials
     */
    public function testLastLoginDateTime(): void
    {
        $testUser = $this->createTestUser();

        $client = static::createClient();

        $client->request('GET', '/login/');

        $crawler = $client->followRedirect();

        $this->assertNull($testUser->getLastLogin());

        $form = $crawler->filter('.form-horizontal')->form();

        $form->setValues([
            '_username' => $testUser->getUsername(),
            '_password' => 'test-123456',
        ]);

        $client->submit($form);

        /** @var User $testUser */
        $testUser = self::$container->get('doctrine')->getRepository(User::class)->findOneByEmail($testUser->getEmail());

        $this->assertEqualsWithDelta(new \DateTime(), $testUser->getLastLogin(), 1.5);
    }

    /**
     * @depends testLastLoginDateTime
     */
    public function testLastLoginDateTimeTwice(): void
    {
        $testUser = $this->createTestUser();

        $client = static::createClient();

        $client->request('GET', '/login/');

        $crawler = $client->followRedirect();

        $this->assertNull($testUser->getLastLogin());

        $form = $crawler->filter('.form-horizontal')->form();

        $form->setValues([
            '_username' => $testUser->getUsername(),
            '_password' => 'test-123456',
        ]);

        $client->submit($form);

        /** @var User $testUser */
        $testUser = self::$container->get('doctrine')->getRepository(User::class)->findOneByEmail($testUser->getEmail());

        $firstLoginDateTime = $testUser->getLastLogin();

        $this->assertEqualsWithDelta(new \DateTime(), $testUser->getLastLogin(), 1.5);

        $client->request('GET', '/logout/');

        $client->request('GET', '/login/');

        $crawler = $client->followRedirect();

        $form = $crawler->filter('.form-horizontal')->form();

        $form->setValues([
            '_username' => $testUser->getUsername(),
            '_password' => 'test-123456',
        ]);

        $client->submit($form);

        /** @var User $testUser */
        $testUser = self::$container->get('doctrine')->getRepository(User::class)->findOneByEmail($testUser->getEmail());

        $secondLoginDateTime = $testUser->getLastLogin();

        $this->assertEqualsWithDelta(new \DateTime(), $testUser->getLastLogin(), 1.5);
        $this->assertNotEquals($firstLoginDateTime, $secondLoginDateTime);
    }

    /**
     * @depends testLoginWithRightCredentials
     */
    public function testLogout(): void
    {
        $testUser = $this->createTestUser();

        $client = static::createClient();

        $client->request('GET', '/login/');

        $crawler = $client->followRedirect();

        $this->assertSelectorExists('body.not-logged-in');

        $form = $crawler->filter('.form-horizontal')->form();

        $form->setValues([
            '_username' => $testUser->getUsername(),
            '_password' => 'test-123456',
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorExists('body.logged-in');

        $client->request('GET', '/login/');

        $this->assertEquals(301, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorExists('body.not-logged-in');
    }
}