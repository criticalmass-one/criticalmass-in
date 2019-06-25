<?php declare(strict_types=1);

namespace Tests\Controller\Security;

use App\Entity\User;
use Tests\Controller\AbstractControllerTest;

class ResettingControllerTest extends AbstractControllerTest
{
    public function testResettingFormVisible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/resetting/request/');

        $this->assertEquals(301, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', 'Kennwort vergessen?');
        $this->assertSelectorExists('input[name=username]');
    }

    /**
     * @depends testResettingFormVisible
     */
    public function testResettingFormWithNewUser(): void
    {
        $client = static::createClient();
        $testUser = $this->createTestUser();

        $client->request('GET', '/resetting/request/');

        $crawler = $client->followRedirect();

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

    /**
     * @depends testResettingFormVisible
     */
    public function testResettingFormWithNonExistingUser(): void
    {
        $client = static::createClient();
        $testUser = $this->createTestUser();

        $client->request('GET', '/resetting/request/');

        $crawler = $client->followRedirect();

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

    /**
     * @depends testResettingFormVisible
     */
    public function testResettingConfirmationMail(): void
    {
        $client = static::createClient();
        $testUser = $this->createTestUser();

        $client->request('GET', '/resetting/request/');

        $crawler = $client->followRedirect();

        $form = $crawler->filter('.fos_user_resetting_request')->form();

        $form->setValues([
            'username' => $testUser->getUsername(),
        ]);

        $client->enableProfiler();

        $client->submit($form);

        /** @var User $testUser */
        $testUser = self::$container->get('doctrine')->getRepository(User::class)->findOneByEmail($testUser->getEmail());

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $client->followRedirect();

        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('Passwort zurücksetzen', $message->getSubject());
        $this->assertSame('malte@caldera.cc', key($message->getFrom()));
        $this->assertSame($testUser->getEmail(), key($message->getTo()));
        $this->assertSame(sprintf('Hallo %s!

Besuchen Sie bitte folgende Seite, um Ihr Passwort zurückzusetzen: http://localhost/resetting/reset/%s

Mit besten Grüßen,
das Team.', $testUser->getUsername(), $testUser->getConfirmationToken()),
            $message->getBody()
        );
    }

    /**
     * @depends testResettingConfirmationMail
     */
    public function testResettingConfirmationMailLink(): void
    {
        $client = static::createClient();
        $testUser = $this->createTestUser();

        $client->request('GET', '/resetting/request/');

        $crawler = $client->followRedirect();

        $form = $crawler->filter('.fos_user_resetting_request')->form();

        $form->setValues([
            'username' => $testUser->getUsername(),
        ]);

        $client->enableProfiler();

        $client->submit($form);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $client->followRedirect();

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        preg_match_all('/http:\/\/localhost\/resetting\/reset\/(.*)/', $message->getBody(), $matches);

        $confirmationUrl = $matches[0][0];

        $confirmationUrl = str_replace('localhost', 'criticalmass.cm', $confirmationUrl);

        $client->request('GET', $confirmationUrl);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @depends testResettingConfirmationMailLink
     */
    public function testNewPasswordForm(): void
    {
        $client = static::createClient();
        $testUser = $this->createTestUser();

        $client->request('GET', '/resetting/request/');

        $crawler = $client->followRedirect();

        $form = $crawler->filter('.fos_user_resetting_request')->form();

        $form->setValues([
            'username' => $testUser->getUsername(),
        ]);

        $client->enableProfiler();

        $client->submit($form);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $client->followRedirect();

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        preg_match_all('/http:\/\/localhost\/resetting\/reset\/(.*)/', $message->getBody(), $matches);

        $confirmationUrl = $matches[0][0];

        $confirmationUrl = str_replace('localhost', 'criticalmass.cm', $confirmationUrl);

        $client->request('GET', $confirmationUrl);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h2', 'Kennwort vergessen?');
        $this->assertSelectorExists('input#fos_user_resetting_form_plainPassword_first');
        $this->assertSelectorExists('input#fos_user_resetting_form_plainPassword_second');
    }

    /**
     * @depends testNewPasswordForm
     */
    public function testNewPasswordSetting(): void
    {
        $client = static::createClient();
        $testUser = $this->createTestUser();

        $client->request('GET', '/resetting/request/');

        $crawler = $client->followRedirect();

        $form = $crawler->filter('.fos_user_resetting_request')->form();

        $form->setValues([
            'username' => $testUser->getUsername(),
        ]);

        $client->enableProfiler();

        $client->submit($form);

        /** @var User $testUser */
        $testUser = self::$container->get('doctrine')->getRepository(User::class)->findOneByEmail($testUser->getEmail());

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $client->followRedirect();

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        preg_match_all('/http:\/\/localhost\/resetting\/reset\/(.*)/', $message->getBody(), $matches);

        $confirmationUrl = $matches[0][0];

        $confirmationUrl = str_replace('localhost', 'criticalmass.cm', $confirmationUrl);

        $crawler = $client->request('GET', $confirmationUrl);

        $form = $crawler->filter('.fos_user_resetting_reset')->form();

        $form->setValues([
            'fos_user_resetting_form[plainPassword][first]' => 'neues-passwort-123',
            'fos_user_resetting_form[plainPassword][second]' => 'neues-passwort-123',
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('html h2', sprintf('Hej %s, schön, dass du da bist!', $testUser->getUsername()));
        $this->assertSelectorTextContains('html div.alert.alert-dismissable.alert-success', 'Das Passwort wurde erfolgreich zurückgesetzt.');
    }

    /**
     * @depends testNewPasswordSetting
     */
    public function testNewPasswordLogin(): void
    {
        $client = static::createClient();
        $testUser = $this->createTestUser();

        $client->request('GET', '/resetting/request/');

        $crawler = $client->followRedirect();

        $form = $crawler->filter('.fos_user_resetting_request')->form();

        $form->setValues([
            'username' => $testUser->getUsername(),
        ]);

        $client->enableProfiler();

        $client->submit($form);

        /** @var User $testUser */
        $testUser = self::$container->get('doctrine')->getRepository(User::class)->findOneByEmail($testUser->getEmail());

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $client->followRedirect();

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        preg_match_all('/http:\/\/localhost\/resetting\/reset\/(.*)/', $message->getBody(), $matches);

        $confirmationUrl = $matches[0][0];

        $confirmationUrl = str_replace('localhost', 'criticalmass.cm', $confirmationUrl);

        $crawler = $client->request('GET', $confirmationUrl);

        $form = $crawler->filter('.fos_user_resetting_reset')->form();

        $form->setValues([
            'fos_user_resetting_form[plainPassword][first]' => 'neues-passwort-123',
            'fos_user_resetting_form[plainPassword][second]' => 'neues-passwort-123',
        ]);

        $client->submit($form);

        $client->request('GET', '/logout/');

        $client->request('GET', '/login/');

        $form = $crawler->filter('.form-horizontal')->form();

        $form->setValues([
            '_username' => $testUser->getUsername(),
            '_password' => 'neues-passwort-123',
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(301, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorExists('body.logged-in');
    }
}