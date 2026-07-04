<?php declare(strict_types=1);

namespace Tests\Criticalmass\FriendlyCaptcha;

use App\Criticalmass\FriendlyCaptcha\FriendlyCaptcha;
use App\Criticalmass\FriendlyCaptcha\Response as CaptchaResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * #1392: Der Captcha-Check muss fail-closed sein — bei einem nicht erfolgreichen
 * Verify-Aufruf (≠200 oder Transportfehler) gilt das Captcha als NICHT bestanden.
 */
final class FriendlyCaptchaTest extends TestCase
{
    private function captcha(HttpClientInterface $httpClient, ?SerializerInterface $serializer = null): FriendlyCaptcha
    {
        $containerBag = $this->createMock(ContainerBagInterface::class);
        $containerBag->method('get')->willReturnMap([
            ['friendlycaptcha_api_key', 'test-api-key'],
            ['friendlycaptcha_site_key', 'test-site-key'],
        ]);

        return new FriendlyCaptcha($serializer ?? $this->createMock(SerializerInterface::class), $httpClient, $containerBag);
    }

    private function request(): Request
    {
        return Request::create('/login', 'POST', ['frc-captcha-solution' => 'solved']);
    }

    public function testNonSuccessStatusFailsClosed(): void
    {
        $captcha = $this->captcha(new MockHttpClient(new MockResponse('error', ['http_code' => 500])));

        self::assertFalse($captcha->checkCaptcha($this->request()));
    }

    public function testTransportExceptionFailsClosed(): void
    {
        $client = new MockHttpClient(static function (): MockResponse {
            throw new \Symfony\Component\HttpClient\Exception\TransportException('network down');
        });

        self::assertFalse($this->captcha($client)->checkCaptcha($this->request()));
    }

    public function testValidSolutionPasses(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('deserialize')->willReturn((new CaptchaResponse())->setSuccess(true)->setErrors(null));

        $captcha = $this->captcha(
            new MockHttpClient(new MockResponse('{"success":true}', ['http_code' => 200])),
            $serializer,
        );

        self::assertTrue($captcha->checkCaptcha($this->request()));
    }

    public function testRejectedSolutionFails(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('deserialize')->willReturn((new CaptchaResponse())->setSuccess(false)->setErrors(['bad']));

        $captcha = $this->captcha(
            new MockHttpClient(new MockResponse('{"success":false}', ['http_code' => 200])),
            $serializer,
        );

        self::assertFalse($captcha->checkCaptcha($this->request()));
    }
}
