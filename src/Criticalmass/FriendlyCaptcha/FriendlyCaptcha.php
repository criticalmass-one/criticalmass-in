<?php declare(strict_types=1);

namespace App\Criticalmass\FriendlyCaptcha;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class FriendlyCaptcha implements FriendlyCaptchaInterface
{
    private const string API_URL = 'https://api.friendlycaptcha.com/api/v1/siteverify';
    private readonly string $apiKey;
    private readonly string $siteKey;

    public function __construct(
        private readonly SerializerInterface $serializer,
        ContainerBagInterface $containerBag
    ) {
        $this->apiKey = $containerBag->get('friendlycaptcha_api_key');
        $this->siteKey = $containerBag->get('friendlycaptcha_site_key');
    }

    public function checkCaptcha(Request $request): bool
    {
        $captchaSolution = $request->request->get('frc-captcha-solution');

        $client = HttpClient::create();
        $response = $client->request('POST', self::API_URL, [
            'json' => [
                'solution' => $captchaSolution,
                'secret' => $this->apiKey,
                'sitekey' => $this->siteKey,
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            return true;
        }

        $result = $this->serializer->deserialize($response->getContent(), Response::class, 'json');

        return $result->isSuccess();
    }
}
