<?php declare(strict_types=1);

namespace Tests\Controller\Api\SocialNetworkProfileApi;

use App\Entity\SocialNetworkProfile;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class SocialNetworkProfileApiTest extends AbstractApiControllerTestCase
{
    public function testListSocialNetworkProfilesReturnsPaginatedResponse(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('meta', $response);
        $this->assertIsArray($response['data']);
        $this->assertArrayHasKey('page', $response['meta']);
        $this->assertArrayHasKey('size', $response['meta']);
        $this->assertArrayHasKey('totalItems', $response['meta']);
        $this->assertArrayHasKey('totalPages', $response['meta']);
    }

    public function testListSocialNetworkProfilesDefaultPagination(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertEquals(0, $response['meta']['page']);
        $this->assertEquals(100, $response['meta']['size']);
    }

    public function testListSocialNetworkProfilesCustomPagination(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles?page=0&size=2');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertEquals(0, $response['meta']['page']);
        $this->assertEquals(2, $response['meta']['size']);
        $this->assertLessThanOrEqual(2, count($response['data']));
    }

    public function testListSocialNetworkProfilesHasData(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertNotEmpty($response['data']);
        $this->assertArrayHasKey('network', $response['data'][0]);
    }

    public function testListSocialNetworkProfilesForHamburg(): void
    {
        $this->client->request('GET', '/api/hamburg/socialnetwork-profiles');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        $networks = array_map(fn(array $profile) => $profile['network'], $response);
        $this->assertContains('twitter', $networks);
        $this->assertContains('facebook_page', $networks);
        $this->assertContains('instagram_profile', $networks);
    }

    public function testListSocialNetworkProfilesForBerlin(): void
    {
        $this->client->request('GET', '/api/berlin/socialnetwork-profiles');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        $networks = array_map(fn(array $profile) => $profile['network'], $response);
        $this->assertContains('twitter', $networks);
        $this->assertContains('mastodon', $networks);
    }

    public function testListSocialNetworkProfilesFilterByNetwork(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles', ['networkIdentifier' => 'twitter']);

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('data', $response);
        $this->assertNotEmpty($response['data']);

        foreach ($response['data'] as $profile) {
            $this->assertEquals('twitter', $profile['network']);
        }
    }

    public function testListSocialNetworkProfilesForUnknownCityReturns404(): void
    {
        $this->client->request('GET', '/api/unknown-city/socialnetwork-profiles');

        $this->assertResponseStatusCode(404);
    }

    public function testSocialNetworkProfileHasExpectedProperties(): void
    {
        $profiles = $this->entityManager->getRepository(SocialNetworkProfile::class)->findAll();

        $this->assertNotEmpty($profiles);

        /** @var SocialNetworkProfile $profile */
        $profile = $profiles[0];

        $this->assertNotNull($profile->getId());
        $this->assertNotNull($profile->getNetwork());
        $this->assertNotNull($profile->getIdentifier());
        $this->assertNotNull($profile->getCity());
    }

    public function testCreateSocialNetworkProfile(): void
    {
        $profileData = [
            'network' => 'bluesky',
            'identifier' => 'criticalmass.bsky.social',
        ];

        $this->client->request(
            'PUT',
            '/api/hamburg/socialnetwork-profiles',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($profileData)
        );

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertArrayHasKey('network', $response);
        $this->assertEquals('bluesky', $response['network']);
        $this->assertEquals('criticalmass.bsky.social', $response['identifier']);
    }

    public function testProfilesHaveCorrectCityAssociation(): void
    {
        $profiles = $this->entityManager->getRepository(SocialNetworkProfile::class)->findAll();

        foreach ($profiles as $profile) {
            $city = $profile->getCity();
            $this->assertNotNull($city);
            $this->assertNotEmpty($city->getCity());
        }
    }
}
