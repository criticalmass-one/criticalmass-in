<?php declare(strict_types=1);

namespace App\Criticalmass\FriendlyCaptcha;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FriendlyCaptcha implements FriendlyCaptchaInterface
{
    private const string API_URL = 'https://api.friendlycaptcha.com/api/v1/siteverify';
    private readonly string $apiKey;
    private readonly string $siteKey;

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly HttpClientInterface $httpClient,
        ContainerBagInterface $containerBag,
    ) {
        $this->apiKey = $containerBag->get('friendlycaptcha_api_key');
        $this->siteKey = $containerBag->get('friendlycaptcha_site_key');
    }

    public function checkCaptcha(Request $request): bool
    {
        $captchaSolution = $request->request->get('frc-captcha-solution');

        try {
            $response = $this->httpClient->request('POST', self::API_URL, [
                'json' => [
                    'solution' => $captchaSolution,
                    'secret' => $this->apiKey,
                    'sitekey' => $this->siteKey,
                ],
            ]);

            // Fail-closed: bei einem nicht erfolgreichen Verify-Aufruf gilt das
            // Captcha als NICHT bestanden (vorher wurde hier `true` zurückgegeben).
            if ($response->getStatusCode() !== 200) {
                return false;
            }

            $content = $response->getContent();
        } catch (HttpClientExceptionInterface) {
            return false;
        }

        /** @var Response $result */
        $result = $this->serializer->deserialize($content, Response::class, 'json');

        return $result->isSuccess();
    }
}
