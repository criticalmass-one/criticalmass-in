<?php declare(strict_types=1);

namespace Tests\Controller\Api\SocialNetworkProfileApi;

use App\Entity\SocialNetworkProfile;
use Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use Tests\Controller\Api\Schema\JsonStructureValidator;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('SocialNetworkProfile API Schema Validation')]
class SocialNetworkProfileApiSchemaTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/socialnetwork-profiles returns array of profiles matching SOCIAL_NETWORK_PROFILE_SCHEMA')]
    public function testSocialNetworkProfileListResponseSchema(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Social network profile list should not be empty');

        foreach ($response as $index => $profile) {
            $this->assertIsArray($profile, "Profile at index {$index} should be an array");
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::SOCIAL_NETWORK_PROFILE_SCHEMA,
                $profile,
                "profiles[{$index}]"
            );
        }
    }

    #[TestDox('GET /api/{citySlug}/socialnetwork-profiles returns city profiles')]
    public function testCitySocialNetworkProfileListResponseSchema(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-profiles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);

        foreach ($response as $index => $profile) {
            $this->assertIsArray($profile, "Profile at index {$index} should be an array");
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::SOCIAL_NETWORK_PROFILE_SCHEMA,
                $profile,
                "profiles[{$index}]"
            );
        }
    }

    #[TestDox('Profile network is a valid network identifier')]
    public function testProfileNetworkIsValid(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $validNetworks = ['facebook', 'twitter', 'instagram', 'youtube', 'mastodon', 'bluesky', 'tumblr'];

        foreach ($response as $profile) {
            $this->assertIsString($profile['network']);
            $this->assertContains(
                $profile['network'],
                $validNetworks,
                sprintf("Invalid network: %s", $profile['network'])
            );
        }
    }

    #[TestDox('Profile identifier is a non-empty string')]
    public function testProfileIdentifierIsNonEmptyString(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $profile) {
            $this->assertIsString($profile['identifier']);
            $this->assertNotEmpty($profile['identifier']);
        }
    }

    #[TestDox('Profile city_id is a positive integer')]
    public function testProfileCityIdIsPositiveInteger(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $profile) {
            $this->assertIsInt($profile['city_id']);
            $this->assertGreaterThan(0, $profile['city_id']);
        }
    }

    #[TestDox('Profile autoFetch and autoPublish are booleans')]
    public function testProfileBooleanFields(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $profile) {
            $this->assertIsBool($profile['auto_fetch']);
            $this->assertIsBool($profile['auto_publish']);
        }
    }

    #[TestDox('Profile timestamps are date-time strings when present')]
    public function testProfileTimestampsAreValidWhenPresent(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $profile) {
            if (isset($profile['created_at']) && $profile['created_at'] !== null) {
                $this->assertIsString($profile['created_at']);
                $this->assertNotFalse(strtotime($profile['created_at']), 'created_at should be a valid date-time');
            }
            if (isset($profile['last_fetch_success_date_time']) && $profile['last_fetch_success_date_time'] !== null) {
                $this->assertIsString($profile['last_fetch_success_date_time']);
                $this->assertNotFalse(strtotime($profile['last_fetch_success_date_time']), 'last_fetch_success_date_time should be a valid date-time');
            }
            if (isset($profile['last_fetch_failure_date_time']) && $profile['last_fetch_failure_date_time'] !== null) {
                $this->assertIsString($profile['last_fetch_failure_date_time']);
                $this->assertNotFalse(strtotime($profile['last_fetch_failure_date_time']), 'last_fetch_failure_date_time should be a valid date-time');
            }
        }
    }

    #[TestDox('GET /api/{invalidCity}/socialnetwork-profiles returns 404')]
    public function testSocialNetworkProfileListForInvalidCityReturns404(): void
    {
        $this->client->request('GET', '/api/nonexistent-city/socialnetwork-profiles');
        $this->assertResponseStatusCode(404);
    }

    #[TestDox('PUT /api/{citySlug}/socialnetwork-profiles creates new profile')]
    public function testCreateSocialNetworkProfile(): void
    {
        $newProfileData = [
            'identifier' => 'test_profile_' . uniqid(),
            'network' => 'twitter',
            'auto_fetch' => false,
            'auto_publish' => false,
        ];

        $this->client->request(
            'PUT',
            '/api/hamburg/socialnetwork-profiles',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($newProfileData)
        );

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
    }

    #[TestDox('POST /api/{citySlug}/socialnetwork-profiles/{id} updates profile')]
    public function testUpdateSocialNetworkProfile(): void
    {
        $profiles = $this->entityManager->getRepository(SocialNetworkProfile::class)->findAll();

        if (empty($profiles)) {
            $this->markTestSkipped('No social network profiles found');
        }

        $profile = $profiles[0];
        $city = $profile->getCity();
        $citySlug = $city->getMainSlugString();

        $updateData = [
            'auto_fetch' => true,
        ];

        $this->client->request(
            'POST',
            sprintf('/api/%s/socialnetwork-profiles/%d', $citySlug, $profile->getId()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updateData)
        );

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertIsArray($response);
    }

    #[TestDox('Profile id is a positive integer')]
    public function testProfileIdIsPositiveInteger(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $profile) {
            $this->assertIsInt($profile['id']);
            $this->assertGreaterThan(0, $profile['id']);
        }
    }
}
