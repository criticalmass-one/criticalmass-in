<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use Tests\Controller\Api\Schema\JsonStructureValidator;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('City API Schema Validation')]
class CityApiSchemaTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/city returns array of cities matching CITY_LIST_ITEM_SCHEMA')]
    public function testCityListResponseSchema(): void
    {
        $this->client->request('GET', '/api/city');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'City list should not be empty');

        // Validate each city matches the schema
        foreach ($response as $index => $city) {
            $this->assertIsArray($city, "City at index {$index} should be an array");
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::CITY_LIST_ITEM_SCHEMA,
                $city,
                "cities[{$index}]"
            );
        }
    }

    #[TestDox('GET /api/{citySlug} returns city matching CITY_DETAIL_SCHEMA')]
    public function testCityDetailResponseSchema(): void
    {
        $this->client->request('GET', '/api/hamburg');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        JsonStructureValidator::assertMatchesSchema(
            ApiSchemaDefinitions::CITY_DETAIL_SCHEMA,
            $response
        );
    }

    #[TestDox('City color is properly structured with RGB values')]
    public function testCityColorStructure(): void
    {
        $this->client->request('GET', '/api/hamburg');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('color', $response);
        $this->assertIsArray($response['color']);

        // Validate color values are in valid range
        $color = $response['color'];
        $this->assertGreaterThanOrEqual(0, $color['red']);
        $this->assertLessThanOrEqual(255, $color['red']);
        $this->assertGreaterThanOrEqual(0, $color['green']);
        $this->assertLessThanOrEqual(255, $color['green']);
        $this->assertGreaterThanOrEqual(0, $color['blue']);
        $this->assertLessThanOrEqual(255, $color['blue']);
    }

    #[TestDox('City mainSlug is properly structured')]
    public function testCityMainSlugStructure(): void
    {
        $this->client->request('GET', '/api/hamburg');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('main_slug', $response);
        JsonStructureValidator::assertMatchesSchema(
            ApiSchemaDefinitions::CITY_SLUG_SCHEMA,
            $response['main_slug'],
            'main_slug'
        );
    }

    #[TestDox('City slugs array contains valid CitySlug objects')]
    public function testCitySlugsArrayStructure(): void
    {
        $this->client->request('GET', '/api/hamburg');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('slugs', $response);
        $this->assertIsArray($response['slugs']);
        $this->assertNotEmpty($response['slugs'], 'City should have at least one slug');

        foreach ($response['slugs'] as $index => $slug) {
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::CITY_SLUG_SCHEMA,
                $slug,
                "slugs[{$index}]"
            );
        }
    }

    #[TestDox('City coordinates are valid numbers')]
    public function testCityCoordinatesAreValid(): void
    {
        $this->client->request('GET', '/api/hamburg');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        // Latitude should be between -90 and 90
        $this->assertGreaterThanOrEqual(-90, $response['latitude']);
        $this->assertLessThanOrEqual(90, $response['latitude']);

        // Longitude should be between -180 and 180
        $this->assertGreaterThanOrEqual(-180, $response['longitude']);
        $this->assertLessThanOrEqual(180, $response['longitude']);
    }

    #[TestDox('GET /api/{invalidSlug} returns 404')]
    public function testCityNotFoundReturns404(): void
    {
        $this->client->request('GET', '/api/nonexistent-city-slug');
        $this->assertResponseStatusCode(404);
    }

    #[TestDox('City socialNetworkProfiles is an array')]
    public function testCitySocialNetworkProfilesIsArray(): void
    {
        $this->client->request('GET', '/api/hamburg');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('social_network_profiles', $response);
        $this->assertIsArray($response['social_network_profiles']);
    }

    #[TestDox('City timezone is a valid timezone string')]
    public function testCityTimezoneIsValid(): void
    {
        $this->client->request('GET', '/api/hamburg');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('timezone', $response);
        $this->assertIsString($response['timezone']);

        // Verify it's a valid PHP timezone
        $validTimezones = \DateTimeZone::listIdentifiers();
        $this->assertContains($response['timezone'], $validTimezones, "Timezone '{$response['timezone']}' is not a valid timezone");
    }
}
