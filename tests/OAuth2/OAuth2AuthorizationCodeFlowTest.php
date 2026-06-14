<?php declare(strict_types=1);

namespace Tests\OAuth2;

use App\Entity\User;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\Client;
use League\Bundle\OAuth2ServerBundle\ValueObject\Grant;
use League\Bundle\OAuth2ServerBundle\ValueObject\RedirectUri;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Spielt den vollständigen Authorization-Code-+PKCE-Flow durch, wie ihn ein
 * MCP-Connector (ChatGPT/Claude) als public Client nutzt:
 *
 *   eingeloggter User → GET /authorize → Consent-Seite → POST /oauth2/consent
 *   → Redirect zurück auf /authorize → Authorization Code → POST /token
 *   → Access Token → GET /mcp/health (200, echter User).
 */
final class OAuth2AuthorizationCodeFlowTest extends WebTestCase
{
    private const REDIRECT_URI = 'https://connector.example.org/callback';
    private const USER_EMAIL = 'mcp-tester@example.org';

    public function testFullAuthorizationCodeFlowGrantsAccessToMcp(): void
    {
        $client = static::createClient();
        // Session + In-Memory-OAuth-Persistenz müssen über alle Requests bestehen.
        $client->disableReboot();
        // Fehler im Flow direkt als Test-Error mit Stacktrace sichtbar machen.
        $client->catchExceptions(false);

        $user = $this->createUser($client);
        $client->loginUser($user, 'user');
        $this->registerPublicAuthCodeClient($client);

        $codeVerifier = 'mcp-poc-verifier-0123456789-0123456789-0123456789';
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        $authorizeUrl = '/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id' => 'mcp-connector',
            'redirect_uri' => self::REDIRECT_URI,
            'scope' => 'ride:read',
            'state' => 'state-123',
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        // 1) /authorize → Consent-Seite (User eingeloggt, noch keine Zustimmung).
        $crawler = $client->request('GET', $authorizeUrl);
        self::assertSame(200, $client->getResponse()->getStatusCode());
        $consentAuthorizeUrl = $crawler->filter('input[name="authorize_url"]')->attr('value');

        // 2) Zustimmung erteilen.
        $client->request('POST', '/oauth2/consent', [
            'client_id' => 'mcp-connector',
            'authorize_url' => $consentAuthorizeUrl,
            'consent' => 'approve',
        ]);
        self::assertTrue($client->getResponse()->isRedirect());

        // 3) Redirect zurück auf /authorize → Authorization Code im Callback-Redirect.
        $client->followRedirect();
        self::assertTrue($client->getResponse()->isRedirect(), 'erwarteter Redirect zur redirect_uri');
        $location = (string) $client->getResponse()->headers->get('Location');
        self::assertStringStartsWith(self::REDIRECT_URI, $location);

        parse_str((string) parse_url($location, PHP_URL_QUERY), $callbackParams);
        self::assertSame('state-123', $callbackParams['state'] ?? null);
        self::assertArrayHasKey('code', $callbackParams);

        // 4) Code gegen Access Token tauschen (public Client → kein Secret, PKCE-Verifier).
        $client->request('POST', '/token', [
            'grant_type' => 'authorization_code',
            'client_id' => 'mcp-connector',
            'redirect_uri' => self::REDIRECT_URI,
            'code' => $callbackParams['code'],
            'code_verifier' => $codeVerifier,
        ]);
        self::assertSame(200, $client->getResponse()->getStatusCode(), (string) $client->getResponse()->getContent());
        $token = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertIsArray($token);
        self::assertArrayHasKey('access_token', $token);
        self::assertArrayHasKey('refresh_token', $token);

        // 5) Geschützten MCP-Endpunkt mit dem User-Token aufrufen → 200.
        $client->request('GET', '/mcp/health', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token['access_token'],
        ]);
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $health = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertIsArray($health);
        self::assertSame('ok', $health['status'] ?? null);
        self::assertSame(self::USER_EMAIL, $health['user'] ?? null);
        self::assertContains('ROLE_USER', $health['roles'] ?? []);
    }

    private function createUser(KernelBrowser $client): User
    {
        $em = $client->getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setUsername('mcp-tester');
        $user->setEmail(self::USER_EMAIL);
        $user->setRoles(['ROLE_USER']);
        $user->setEnabled(true);

        $em->persist($user);
        $em->flush();

        return $user;
    }

    private function registerPublicAuthCodeClient(KernelBrowser $client): void
    {
        /** @var ClientManagerInterface $clientManager */
        $clientManager = $client->getContainer()->get(ClientManagerInterface::class);

        $oauthClient = new Client('MCP Connector', 'mcp-connector', null);
        $oauthClient->setActive(true);
        $oauthClient->setGrants(new Grant('authorization_code'), new Grant('refresh_token'));
        $oauthClient->setRedirectUris(new RedirectUri(self::REDIRECT_URI));
        $oauthClient->setScopes(new Scope('ride:read'));
        $clientManager->save($oauthClient);
    }
}
