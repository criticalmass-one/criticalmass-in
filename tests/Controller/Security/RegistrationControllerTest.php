<?php declare(strict_types=1);

namespace Tests\Controller\Security;

use App\Controller\AbstractController;
use App\Entity\User;

class RegistrationControllerTest extends AbstractController
{
    protected function createTestUser(): User
    {
        $user = new User();
        $user
            ->setUsername(uniqid('criticalmass-test-', false))
            ->setEmail($email = sprintf('%s@caldera.cc', $user->getUsername()))
            ->setPlainPassword('test-123456');

        return $user;
    }

    public function testRegistrationFormVisible(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1', 'Auf die Räder, fertig, los!');
        $this->assertEquals(4, $crawler->filter('.fos_user_registration_register input.form-control')->count());
        $this->assertEquals(5, $crawler->filter('.fos_user_registration_register input')->count());
    }

    /**
     * @depends testRegistrationFormVisible
     */
    public function testRegistrationForm(): void
    {
        $testUser = $this->createTestUser();

        $client = static::createClient();

        $crawler = $client->request('GET', '/register/');
        $form = $crawler->filter('.fos_user_registration_register')->form();

        $form->setValues([
            'fos_user_registration_form[username]' => $testUser->getUsername(),
            'fos_user_registration_form[email]' => $testUser->getEmail(),
            'fos_user_registration_form[plainPassword][first]' => $testUser->getPlainPassword(),
            'fos_user_registration_form[plainPassword][second]' => $testUser->getPlainPassword(),
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', sprintf('Hej %s!', $testUser->getUsername()));
    }

    /**
     * @depends testRegistrationForm
     */
    public function testConfirmationMail(): void
    {
        $testUser = $this->createTestUser();

        $client = static::createClient();

        $crawler = $client->request('GET', '/register/');
        $form = $crawler->filter('.fos_user_registration_register')->form();

        $form->setValues([
            'fos_user_registration_form[username]' => $testUser->getUsername(),
            'fos_user_registration_form[email]' => $testUser->getEmail(),
            'fos_user_registration_form[plainPassword][first]' => $testUser->getPlainPassword(),
            'fos_user_registration_form[plainPassword][second]' => $testUser->getPlainPassword(),
        ]);

        $client->enableProfiler();

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $client->followRedirect();

        /** @var User $user */
        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneByEmail($testUser->getEmail());

        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame(sprintf('Willkommen %s!', $testUser->getUsername()), $message->getSubject());
        $this->assertSame('malte@caldera.cc', key($message->getFrom()));
        $this->assertSame($testUser->getEmail(), key($message->getTo()));
        $this->assertSame(sprintf('Hallo %s!

Besuchen Sie bitte folgende Seite, um Ihr Benutzerkonto zu bestätigen: http://localhost/register/confirm/%s

Mit besten Grüßen,
das Team.', $testUser->getUsername(), $user->getConfirmationToken()),
            $message->getBody()
        );
    }

    /**
     * @depends testConfirmationMail
     */
    public function testConfirmation(): void
    {
        $testUser = $this->createTestUser();

        $client = static::createClient();

        $crawler = $client->request('GET', '/register/');
        $form = $crawler->filter('.fos_user_registration_register')->form();

        $form->setValues([
            'fos_user_registration_form[username]' => $testUser->getUsername(),
            'fos_user_registration_form[email]' => $testUser->getEmail(),
            'fos_user_registration_form[plainPassword][first]' => $testUser->getPlainPassword(),
            'fos_user_registration_form[plainPassword][second]' => $testUser->getPlainPassword(),
        ]);

        $client->enableProfiler();

        $client->submit($form);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $client->followRedirect();

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        preg_match_all('/http:\/\/localhost\/register\/confirm\/(.*)/', $message->getBody(), $matches);

        $this->assertCount(2, $matches);

        $confirmationUrl = $matches[0][0];

        $confirmationUrl = str_replace('localhost', 'criticalmass.cm', $confirmationUrl);

        $client->request('GET', $confirmationUrl);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', sprintf('Hej %s! Willkommen in der Masse!', $testUser->getUsername()));
    }
}