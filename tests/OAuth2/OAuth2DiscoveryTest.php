<?php declare(strict_types=1);

namespace Tests\OAuth2;

use App\OAuth2\OAuthScope;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Verifiziert die für MCP-Connectoren nötigen Discovery- und
 * Registrierungs-Endpunkte (RFC 9728 / 8414 / 7591).
 */
final class OAuth2DiscoveryTest extends WebTestCase
{
    public function testProtectedResourceMetadataIsPublic(): void
    {
        $client = static::createClient();
        $client->request('GET', '/.well-known/oauth-protected-resource');

        self::assertResponseIsSuccessful();
        $data = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertIsArray($data);
        self::assertStringEndsWith('/mcp', $data['resource']);
        self::assertSame([rtrim($data['resource'], '/mcp')], $data['authorization_servers']);
        self::assertSame(OAuthScope::values(), $data['scopes_supported']);
        self::assertSame(['header'], $data['bearer_methods_supported']);
    }

    public function testAuthorizationServerMetadataIsPublic(): void
    {
        $client = static::createClient();
        $client->request('GET', '/.well-known/oauth-authorization-server');

        self::assertResponseIsSuccessful();
        $data = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertIsArray($data);
        self::assertStringEndsWith('/authorize', $data['authorization_endpoint']);
        self::assertStringEndsWith('/token', $data['token_endpoint']);
        self::assertStringEndsWith('/oauth2/register', $data['registration_endpoint']);
        self::assertSame(['code'], $data['response_types_supported']);
        self::assertContains('authorization_code', $data['grant_types_supported']);
        self::assertSame(['S256'], $data['code_challenge_methods_supported']);
    }

    public function testDynamicClientRegistrationCreatesPublicClient(): void
    {
        $client = static::createClient();
        $client->request('POST', '/oauth2/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'client_name' => 'Claude Connector',
            'redirect_uris' => ['https://claude.ai/api/mcp/auth_callback'],
            'scope' => 'ride:read track:read',
        ], JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(201);
        $data = json_decode((string) $client->getResponse()->getContent(), true);

        self::assertIsArray($data);
        self::assertStringStartsWith('mcp_', $data['client_id']);
        self::assertSame('none', $data['token_endpoint_auth_method']);
        self::assertSame(['https://claude.ai/api/mcp/auth_callback'], $data['redirect_uris']);
        self::assertSame('ride:read track:read', $data['scope']);
        self::assertContains('refresh_token', $data['grant_types']);
    }

    public function testDynamicClientRegistrationRejectsMissingRedirectUri(): void
    {
        $client = static::createClient();
        $client->request('POST', '/oauth2/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['client_name' => 'Broken'], JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(400);
        $data = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertSame('invalid_redirect_uri', $data['error']);
    }
}
