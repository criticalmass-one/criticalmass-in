<?php declare(strict_types=1);

namespace Tests\Controller\Api\TimelineApi;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class TimelineApiTest extends AbstractApiControllerTestCase
{
    #[TestDox('Timeline API returns valid JSON structure with tabs, navigation and period')]
    public function testValidJsonStructure(): void
    {
        $this->client->request('GET', '/api/timeline?year=2024&month=6');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('tabs', $response);
        $this->assertArrayHasKey('navigation', $response);
        $this->assertArrayHasKey('period', $response);
    }

    #[TestDox('Timeline API returns correct content type')]
    public function testContentType(): void
    {
        $this->client->request('GET', '/api/timeline?year=2024&month=6');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    #[TestDox('Timeline API tabs contain arrays of HTML strings')]
    public function testTabsContainHtmlArrays(): void
    {
        $this->client->request('GET', '/api/timeline?year=2024&month=6');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response['tabs']);

        foreach ($response['tabs'] as $tabName => $tabContent) {
            $this->assertIsString($tabName);
            $this->assertIsArray($tabContent);

            foreach ($tabContent as $htmlItem) {
                $this->assertIsString($htmlItem);
            }
        }
    }

    #[TestDox('Timeline API returns correct period data')]
    public function testPeriodData(): void
    {
        $this->client->request('GET', '/api/timeline?year=2024&month=6');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertEquals(2024, $response['period']['year']);
        $this->assertEquals(6, $response['period']['month']);
    }

    #[TestDox('Timeline API returns previous month navigation when not at lower bound')]
    public function testNavigationPreviousMonthPresent(): void
    {
        $this->client->request('GET', '/api/timeline?year=2024&month=6');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertNotNull($response['navigation']['previous']);
        $this->assertEquals(2024, $response['navigation']['previous']['year']);
        $this->assertEquals(5, $response['navigation']['previous']['month']);
    }

    #[TestDox('Timeline API returns null for previous month at lower bound 2010-01')]
    public function testNavigationPreviousMonthNullAtLowerBound(): void
    {
        $this->client->request('GET', '/api/timeline?year=2010&month=1');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertNull($response['navigation']['previous']);
    }

    #[TestDox('Timeline API returns navigation structure')]
    public function testNavigationStructure(): void
    {
        $this->client->request('GET', '/api/timeline?year=2024&month=6');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('previous', $response['navigation']);
        $this->assertArrayHasKey('next', $response['navigation']);
    }

    #[TestDox('Timeline API returns 404 for date before lower bound')]
    public function testReturns404ForDateBeforeLowerBound(): void
    {
        $this->client->request('GET', '/api/timeline?year=2009&month=12');

        $this->assertResponseStatusCode(404);
    }

    #[TestDox('Timeline API returns 404 for invalid month')]
    public function testReturns404ForInvalidMonth(): void
    {
        $this->client->request('GET', '/api/timeline?year=2024&month=13');

        $this->assertResponseStatusCode(404);
    }
}
