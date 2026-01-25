<?php declare(strict_types=1);

namespace Tests\Controller\Api\SocialNetworkProfileApi;

use App\Entity\SocialNetworkProfile;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class SocialNetworkProfileApiTest extends AbstractApiControllerTestCase
{
    public function testListSocialNetworkProfiles(): void
    {
        $this->client->request('GET', '/api/socialnetwork-profiles');

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('network', $response[0]);
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
        $this->assertContains('facebook', $networks);
        $this->assertContains('instagram', $networks);
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

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        foreach ($response as $profile) {
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

    public function testMainNetworkFlag(): void
    {
        $profiles = $this->entityManager->getRepository(SocialNetworkProfile::class)->findAll();

        $mainNetworks = array_filter($profiles, fn(SocialNetworkProfile $profile) => $profile->isMainNetwork());

        $this->assertNotEmpty($mainNetworks, 'At least one profile should be marked as main network');
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
