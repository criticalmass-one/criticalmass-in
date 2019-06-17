<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\User;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResettingControllerTest extends WebTestCase
{
    protected function createTestUser(): User
    {
        /** @var UserManagerInterface $fosUserManager */
        $fosUserManager = self::$container->get('fos_user.user_manager');
        $user = $fosUserManager->createUser();

        $user
            ->setUsername(uniqid('criticalmass-test-', false))
            ->setEmail($email = sprintf('%s@caldera.cc', $user->getUsername()))
            ->setPlainPassword('test-123456');

        $registry = self::$container->get('doctrine');
        $registry->getManager()->persist($user);
        $registry->getManager()->flush();

        $fosUserManager->updateUser($user);

        return $user;
    }

    public function testResettingFormVisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/resetting/request/');

        $this->assertEquals(301, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', 'Kennwort vergessen?');
        $this->assertEquals(1, $crawler->filter('input[name=username]')->count());
    }

    public function testResettingFormWithNewUser(): void
    {
        $client = static::createClient();
        $testUser = $this->createTestUser();

        $client->request('GET', '/resetting/request/');

        $this->assertEquals(301, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', 'Kennwort vergessen?');
        $this->assertEquals(1, $crawler->filter('input[name=username]')->count());

        $form = $crawler->filter('.fos_user_resetting_request')->form();

        $form->setValues([
            'username' => $testUser->getUsername(),
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', 'Okay, rufe jetzt deine E-Mails ab');
    }

    public function testResettingFormWithNonExistingUser(): void
    {
        $client = static::createClient();
        $testUser = $this->createTestUser();

        $client->request('GET', '/resetting/request/');

        $this->assertEquals(301, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', 'Kennwort vergessen?');
        $this->assertEquals(1, $crawler->filter('input[name=username]')->count());

        $form = $crawler->filter('.fos_user_resetting_request')->form();

        $wrongUsername = sprintf('%s-%s', $testUser->getUsername(), uniqid('foo'));

        $form->setValues([
            'username' => $wrongUsername,
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', 'Okay, rufe jetzt deine E-Mails ab');
    }
}