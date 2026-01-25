<?php declare(strict_types=1);

namespace Tests\Controller\Api\CycleApi;

use Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use Tests\Controller\Api\Schema\JsonStructureValidator;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('Cycle API Schema Validation')]
class CycleApiSchemaTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/cycles returns array of cycles matching CYCLE_SCHEMA')]
    public function testCycleListResponseSchema(): void
    {
        $this->client->request('GET', '/api/cycles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Cycle list should not be empty');

        foreach ($response as $index => $cycle) {
            $this->assertIsArray($cycle, "Cycle at index {$index} should be an array");
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::CYCLE_SCHEMA,
                $cycle,
                "cycles[{$index}]"
            );
        }
    }

    #[TestDox('Cycle dayOfWeek is between 0 and 6')]
    public function testCycleDayOfWeekIsValid(): void
    {
        $this->client->request('GET', '/api/cycles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $cycle) {
            $this->assertIsInt($cycle['day_of_week']);
            $this->assertGreaterThanOrEqual(0, $cycle['day_of_week']);
            $this->assertLessThanOrEqual(6, $cycle['day_of_week']);
        }
    }

    #[TestDox('Cycle weekOfMonth is between 0 and 4 when present')]
    public function testCycleWeekOfMonthIsValid(): void
    {
        $this->client->request('GET', '/api/cycles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $cycle) {
            if (isset($cycle['week_of_month']) && $cycle['week_of_month'] !== null) {
                $this->assertIsInt($cycle['week_of_month']);
                $this->assertGreaterThanOrEqual(0, $cycle['week_of_month']);
                $this->assertLessThanOrEqual(4, $cycle['week_of_month']);
            }
        }
    }

    #[TestDox('Cycle time is an ISO 8601 date-time string when present')]
    public function testCycleTimeIsDateTimeString(): void
    {
        $this->client->request('GET', '/api/cycles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $cycle) {
            if (isset($cycle['time']) && $cycle['time'] !== null) {
                $this->assertIsString($cycle['time']);
                // Verify it's a valid ISO 8601 date
                $this->assertNotFalse(strtotime($cycle['time']), 'time should be a valid date-time string');
            }
        }
    }

    #[TestDox('Cycle coordinates are valid when present')]
    public function testCycleCoordinatesAreValidWhenPresent(): void
    {
        $this->client->request('GET', '/api/cycles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $cycle) {
            if (isset($cycle['latitude']) && $cycle['latitude'] !== null) {
                $this->assertGreaterThanOrEqual(-90, $cycle['latitude']);
                $this->assertLessThanOrEqual(90, $cycle['latitude']);
            }
            if (isset($cycle['longitude']) && $cycle['longitude'] !== null) {
                $this->assertGreaterThanOrEqual(-180, $cycle['longitude']);
                $this->assertLessThanOrEqual(180, $cycle['longitude']);
            }
        }
    }

    #[TestDox('Cycle createdAt is an ISO 8601 date-time string')]
    public function testCycleCreatedAtIsDateTimeString(): void
    {
        $this->client->request('GET', '/api/cycles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $cycle) {
            $this->assertArrayHasKey('created_at', $cycle);
            $this->assertIsString($cycle['created_at']);
            $this->assertNotFalse(strtotime($cycle['created_at']), 'created_at should be a valid date-time string');
        }
    }

    #[TestDox('Cycle city relation is present')]
    public function testCycleCityRelationIsPresent(): void
    {
        $this->client->request('GET', '/api/cycles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $cycle) {
            $this->assertArrayHasKey('city', $cycle);
            $this->assertIsArray($cycle['city']);
        }
    }

    #[TestDox('Cycle validFrom and validUntil are date-time strings when present')]
    public function testCycleValidDatesAreDateTimeStrings(): void
    {
        $this->client->request('GET', '/api/cycles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $cycle) {
            if (isset($cycle['valid_from']) && $cycle['valid_from'] !== null) {
                $this->assertIsString($cycle['valid_from']);
                $this->assertNotFalse(strtotime($cycle['valid_from']), 'valid_from should be a valid date-time string');
            }
            if (isset($cycle['valid_until']) && $cycle['valid_until'] !== null) {
                $this->assertIsString($cycle['valid_until']);
                $this->assertNotFalse(strtotime($cycle['valid_until']), 'valid_until should be a valid date-time string');
            }
        }
    }

    #[TestDox('Cycle id is a positive integer')]
    public function testCycleIdIsPositiveInteger(): void
    {
        $this->client->request('GET', '/api/cycles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $cycle) {
            $this->assertIsInt($cycle['id']);
            $this->assertGreaterThan(0, $cycle['id']);
        }
    }
}
