<?php declare(strict_types=1);

namespace Tests\Controller\Api\LocationApi;

use Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use Tests\Controller\Api\Schema\JsonStructureValidator;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('Location API Schema Validation')]
class LocationApiSchemaTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/{citySlug}/location returns array of locations matching LOCATION_SCHEMA')]
    public function testLocationListResponseSchema(): void
    {
        $this->client->request('GET', '/api/hamburg/location');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Location list should not be empty');

        foreach ($response as $index => $location) {
            $this->assertIsArray($location, "Location at index {$index} should be an array");
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::LOCATION_SCHEMA,
                $location,
                "locations[{$index}]"
            );
        }
    }

    #[TestDox('GET /api/{citySlug}/location/{slug} returns location matching LOCATION_SCHEMA')]
    public function testLocationDetailResponseSchema(): void
    {
        // First get the location list to find a valid slug
        $this->client->request('GET', '/api/hamburg/location');
        $this->assertResponseIsSuccessful();

        $locations = $this->getJsonResponse();
        $this->assertNotEmpty($locations);

        $locationSlug = $locations[0]['slug'];

        $this->client->request('GET', sprintf('/api/hamburg/location/%s', $locationSlug));
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        JsonStructureValidator::assertMatchesSchema(
            ApiSchemaDefinitions::LOCATION_SCHEMA,
            $response
        );
    }

    #[TestDox('Location coordinates are valid')]
    public function testLocationCoordinatesAreValid(): void
    {
        $this->client->request('GET', '/api/hamburg/location');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $location) {
            // Latitude should be between -90 and 90
            $this->assertGreaterThanOrEqual(-90, $location['latitude']);
            $this->assertLessThanOrEqual(90, $location['latitude']);

            // Longitude should be between -180 and 180
            $this->assertGreaterThanOrEqual(-180, $location['longitude']);
            $this->assertLessThanOrEqual(180, $location['longitude']);
        }
    }

    #[TestDox('Location slug is a non-empty string')]
    public function testLocationSlugIsNonEmptyString(): void
    {
        $this->client->request('GET', '/api/hamburg/location');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $location) {
            $this->assertIsString($location['slug']);
            $this->assertNotEmpty($location['slug']);
        }
    }

    #[TestDox('Location title is a non-empty string')]
    public function testLocationTitleIsNonEmptyString(): void
    {
        $this->client->request('GET', '/api/hamburg/location');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $location) {
            $this->assertIsString($location['title']);
            $this->assertNotEmpty($location['title']);
        }
    }

    #[TestDox('GET /api/{invalidCity}/location returns 404')]
    public function testLocationListForInvalidCityReturns404(): void
    {
        $this->client->request('GET', '/api/nonexistent-city/location');
        $this->assertResponseStatusCode(404);
    }

    #[TestDox('GET /api/{citySlug}/location/{invalidSlug} returns 404')]
    public function testLocationNotFoundReturns404(): void
    {
        $this->client->request('GET', '/api/hamburg/location/nonexistent-location');
        $this->assertResponseStatusCode(404);
    }

    #[TestDox('Location id is a positive integer')]
    public function testLocationIdIsPositiveInteger(): void
    {
        $this->client->request('GET', '/api/hamburg/location');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $location) {
            $this->assertIsInt($location['id']);
            $this->assertGreaterThan(0, $location['id']);
        }
    }
}
