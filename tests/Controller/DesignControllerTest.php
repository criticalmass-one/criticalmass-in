<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Design\DesignChoice;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class DesignControllerTest extends AbstractControllerTestCase
{
    private const string USER = 'cyclist@criticalmass.in';

    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['FEATURE_DESIGN_V2'] = 'true';
    }

    protected function tearDown(): void
    {
        unset($_ENV['FEATURE_DESIGN_V2']);

        parent::tearDown();
    }

    private function switchTo(KernelBrowser $client, string $design, string $target = '/', bool $sameOrigin = true): void
    {
        $token = static::getContainer()->get('security.csrf.token_manager')->getToken('design-switch')->getValue();

        $client->request('POST', '/ansicht', [
            '_token' => $token,
            'design' => $design,
            '_target' => $target,
        ], [], $sameOrigin ? ['HTTP_SEC_FETCH_SITE' => 'same-origin'] : []);
    }

    public function testRouteIsGoneWhileFlagIsOff(): void
    {
        $client = static::createClient();
        $_ENV['FEATURE_DESIGN_V2'] = 'false';

        $this->switchTo($client, 'v2');

        self::assertResponseStatusCodeSame(404);
    }

    public function testGuestGetsCookieAndIsSentBack(): void
    {
        $client = static::createClient();

        $this->switchTo($client, 'v2', '/hamburg?tab=fotos');

        self::assertResponseRedirects('/hamburg?tab=fotos');

        $cookie = $client->getCookieJar()->get(DesignChoice::COOKIE);
        self::assertNotNull($cookie);
        self::assertSame('v2', $cookie->getValue());
        self::assertTrue($cookie->isHttpOnly());
    }

    public function testForeignTargetLeadsToFrontpage(): void
    {
        $client = static::createClient();

        foreach (['https://example.org/', '//example.org/', '/\\example.org/', "/\t/example.org/", "/\n/example.org/", "/hamburg\n", '/ /example.org/', 'javascript:alert(1)'] as $target) {
            $this->switchTo($client, 'v1', $target);

            self::assertResponseRedirects('/', null, sprintf('Ziel %s', $target));
        }
    }

    public function testCrossSiteRequestIsRejected(): void
    {
        $client = static::createClient();

        $this->switchTo($client, 'v2', '/', false);

        self::assertResponseStatusCodeSame(400);
        self::assertNull($client->getCookieJar()->get(DesignChoice::COOKIE));
    }

    public function testUnknownDesignIsRejected(): void
    {
        $client = static::createClient();

        $this->switchTo($client, 'v3');

        self::assertResponseStatusCodeSame(404);
    }

    public function testChoiceIsStoredAtTheAccount(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER);

        $this->switchTo($client, 'v2');
        self::assertResponseRedirects('/');
        self::assertTrue($this->getUser(self::USER)->getDesignV2());

        $this->switchTo($client, 'v1');
        self::assertFalse($this->getUser(self::USER)->getDesignV2());
    }

    public function testFooterOffersTheSwitchOnlyWithFlag(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');
        self::assertSelectorTextContains('footer', 'Neue Ansicht ausprobieren');

        $_ENV['FEATURE_DESIGN_V2'] = 'false';
        $client->request('GET', '/login');
        self::assertSelectorTextNotContains('footer', 'Neue Ansicht ausprobieren');
    }
}
