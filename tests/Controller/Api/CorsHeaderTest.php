<?php declare(strict_types=1);

namespace Tests\Controller\Api;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

class CorsHeaderTest extends AbstractApiControllerTestCase
{
    #[TestDox('API response contains Access-Control-Allow-Origin header')]
    public function testApiResponseContainsCorsOriginHeader(): void
    {
        $this->client->request('GET', '/api/hamburg');

        $this->assertResponseIsSuccessful();
        $this->assertEquals('*', $this->client->getResponse()->headers->get('Access-Control-Allow-Origin'));
    }

    #[TestDox('API response contains Access-Control-Allow-Methods header')]
    public function testApiResponseContainsCorsMethodsHeader(): void
    {
        $this->client->request('GET', '/api/hamburg');

        $this->assertResponseIsSuccessful();
        $this->assertEquals('GET, OPTIONS', $this->client->getResponse()->headers->get('Access-Control-Allow-Methods'));
    }

    #[TestDox('API response contains Access-Control-Allow-Headers header')]
    public function testApiResponseContainsCorsAllowHeadersHeader(): void
    {
        $this->client->request('GET', '/api/hamburg');

        $this->assertResponseIsSuccessful();
        $this->assertEquals('Content-Type', $this->client->getResponse()->headers->get('Access-Control-Allow-Headers'));
    }

    #[TestDox('Non-API routes do not contain CORS headers')]
    public function testNonApiRouteHasNoCorsHeaders(): void
    {
        $this->client->request('GET', '/hamburg');

        $this->assertNull($this->client->getResponse()->headers->get('Access-Control-Allow-Origin'));
    }

    #[DataProvider('apiEndpointProvider')]
    #[TestDox('CORS headers are present on API endpoint: $endpoint')]
    public function testCorsHeadersOnVariousApiEndpoints(string $endpoint): void
    {
        $this->client->request('GET', $endpoint);

        $this->assertNotNull(
            $this->client->getResponse()->headers->get('Access-Control-Allow-Origin'),
            sprintf('Missing Access-Control-Allow-Origin header on %s', $endpoint)
        );
    }

    /** @return array<string, array{string}> */
    public static function apiEndpointProvider(): array
    {
        return [
            'city endpoint' => ['/api/hamburg'],
            'ride list endpoint' => ['/api/ride?size=1'],
            'photo list endpoint' => ['/api/photo?size=1'],
        ];
    }
}
