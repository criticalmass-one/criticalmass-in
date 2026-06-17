<?php declare(strict_types=1);

namespace Tests\OAuth2;

use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\Client;
use League\Bundle\OAuth2ServerBundle\ValueObject\Grant;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * PoC für den Forward-Port des OAuth2-Authorization-Servers (league/oauth2-server-bundle)
 * auf Symfony 7.2. Verifiziert headless (in-memory persistence, kein DB/Browser),
 * dass der Server unter SF7 ein signiertes JWT-Access-Token am /token-Endpunkt ausgibt.
 */
final class OAuth2TokenIssuanceTest extends WebTestCase
{
    public function testTokenEndpointIssuesSignedJwt(): void
    {
        $client = static::createClient();

        /** @var ClientManagerInterface $clientManager */
        $clientManager = static::getContainer()->get(ClientManagerInterface::class);

        $oauthClient = new Client('MCP PoC', 'mcp-poc', 'poc-secret');
        $oauthClient->setActive(true);
        $oauthClient->setGrants(new Grant('client_credentials'));
        $oauthClient->setScopes(new Scope('ride:read'), new Scope('track:read'));
        $clientManager->save($oauthClient);

        $client->request('POST', '/token', [
            'grant_type' => 'client_credentials',
            'client_id' => 'mcp-poc',
            'client_secret' => 'poc-secret',
            'scope' => 'ride:read track:read',
        ]);

        $response = $client->getResponse();
        self::assertSame(200, $response->getStatusCode(), $response->getContent() ?: '');

        $payload = json_decode((string) $response->getContent(), true);
        self::assertIsArray($payload);
        self::assertSame('Bearer', $payload['token_type'] ?? null);
        self::assertArrayHasKey('access_token', $payload);
        self::assertArrayHasKey('expires_in', $payload);

        // Access-Token ist ein JWT (header.payload.signature) → Signierung greift.
        self::assertCount(3, explode('.', (string) $payload['access_token']));
    }
}
