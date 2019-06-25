<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\User;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AbstractControllerTest extends WebTestCase
{
    protected function login(Client $client, string $username): Client
    {
        /** @var Session $session */
        $session = $client->getContainer()->get('session');

        $firewallName = 'main';
        $firewallContext = 'user';

        $token = new UsernamePasswordToken($username, null, $firewallName, ['ROLE_ADMIN']);
        $token->setUser(new User());

        $session->set(sprintf('_security_%s', $firewallContext), serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;
    }

    protected function loginViaForm(Client $client, string $username, string $password): Client
    {
        $client->request('GET', '/login/');

        $crawler = $client->followRedirect();

        $form = $crawler->filter('.form-horizontal')->form();

        $form->setValues([
            '_username' => $username,
            '_password' => $password,
        ]);

        $client->submit($form);

        $client->followRedirect();

        return $client;
    }

    protected function createTestUser(bool $enabled = true): User
    {
        if (!self::$container) {
            self::bootKernel();
        }

        /** @var UserManagerInterface $fosUserManager */
        $fosUserManager = self::$container->get('fos_user.user_manager');
        $user = $fosUserManager->createUser();

        $user
            ->setUsername(uniqid('criticalmass-test-', false))
            ->setEmail($email = sprintf('%s@caldera.cc', $user->getUsername()))
            ->setPlainPassword('test-123456')
            ->setEnabled($enabled);

        $fosUserManager->updateUser($user);

        return $user;
    }
}