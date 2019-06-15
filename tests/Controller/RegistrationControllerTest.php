<?php declare(strict_types=1);

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationFormVisible(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1', 'Auf die Räder, fertig, los!');
        $this->assertEquals(4, $crawler->filter('.fos_user_registration_register input.form-control')->count());
        $this->assertEquals(5, $crawler->filter('.fos_user_registration_register input')->count());
    }

    public function testRegistrationForm(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register/');

        $username = uniqid('test-', true);
        $email = sprintf('%s@test.criticalmass.in', $username);
        $password = 'test-123456';

        $form = $crawler->filter('.fos_user_registration_register')->form();

        $form->setValues([
            'fos_user_registration_form[username]' => $username,
            'fos_user_registration_form[email]' => $email,
            'fos_user_registration_form[plainPassword][first]' => $password,
            'fos_user_registration_form[plainPassword][second]' => $password,
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', sprintf('Hej %s!', $username));
    }
}