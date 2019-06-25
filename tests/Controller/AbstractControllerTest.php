<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\User;
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
}