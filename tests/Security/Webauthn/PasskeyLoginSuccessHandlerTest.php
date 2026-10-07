<?php declare(strict_types=1);

namespace Tests\Security\Webauthn;

use App\Security\Webauthn\PasskeyLoginSuccessHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;

class PasskeyLoginSuccessHandlerTest extends TestCase
{
    public function testReturnsStoredTargetPathAndForgetsIt(): void
    {
        $request = $this->createRequestWithSession();
        $request->getSession()->set('_security.main.target_path', 'https://criticalmass.in/hamburg/2026-10-30');

        $data = $this->handle($request);

        self::assertSame('ok', $data['status']);
        self::assertSame('https://criticalmass.in/hamburg/2026-10-30', $data['redirectUrl']);
        self::assertFalse($request->getSession()->has('_security.main.target_path'));
    }

    public function testOmitsRedirectWithoutTargetPath(): void
    {
        $data = $this->handle($this->createRequestWithSession());

        self::assertSame('ok', $data['status']);
        self::assertArrayNotHasKey('redirectUrl', $data);
    }

    public function testIgnoresTargetPathOfOtherFirewalls(): void
    {
        $request = $this->createRequestWithSession();
        $request->getSession()->set('_security.mcp.target_path', '/mcp');

        self::assertArrayNotHasKey('redirectUrl', $this->handle($request));
    }

    public function testWorksWithoutSession(): void
    {
        $data = $this->handle(Request::create('/passkey/login', 'POST'));

        self::assertSame('ok', $data['status']);
        self::assertArrayNotHasKey('redirectUrl', $data);
    }

    private function createRequestWithSession(): Request
    {
        $request = Request::create('/passkey/login', 'POST');
        $request->setSession(new Session(new MockArraySessionStorage()));

        return $request;
    }

    /**
     * @return array<string, mixed>
     */
    private function handle(Request $request): array
    {
        $response = (new PasskeyLoginSuccessHandler())->onAuthenticationSuccess($request, new NullToken());

        self::assertSame(200, $response->getStatusCode());

        return json_decode((string) $response->getContent(), true, flags: JSON_THROW_ON_ERROR);
    }
}
