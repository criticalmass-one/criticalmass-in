<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

abstract class AbstractControllerTestCase extends WebTestCase
{
    protected function getUser(string $email): User
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        return $em->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    protected function loginAs(KernelBrowser $client, string $email): void
    {
        $user = $this->getUser($email);
        $client->loginUser($user, 'user');
    }

    protected function loginViaForm(KernelBrowser $client, string $username, string $password): KernelBrowser
    {
        $crawler = $client->request('GET', '/login');

        // Check if we got redirected
        if ($client->getResponse()->isRedirection()) {
            $crawler = $client->followRedirect();
        }

        // Find the login form
        $form = $crawler->filter('form')->form();

        $form->setValues([
            '_username' => $username,
            '_password' => $password,
        ]);

        $client->submit($form);

        if ($client->getResponse()->isRedirection()) {
            $client->followRedirect();
        }

        return $client;
    }
}