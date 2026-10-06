<?php declare(strict_types=1);

namespace Tests\Controller\Api\SocialNetworkProfileApi;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\SocialNetworkProfile;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * Tests des Social-Network-Profil-Delete-Endpunkts. Transaktions-isoliert.
 */
class SocialNetworkDeleteTest extends AbstractApiControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $connection = $this->entityManager->getConnection();

        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        parent::tearDown();
    }

    /**
     * @return array{City, SocialNetworkProfile}
     */
    private function createProfile(): array
    {
        $slug = 'social-api-' . substr(md5(uniqid('', true)), 0, 12);

        $city = new City();
        $city->setCity('Socialstadt');
        $city->setTitle('Critical Mass Socialstadt');
        $city->setCreatedAt(new \DateTime());
        $this->entityManager->persist($city);

        $citySlug = new CitySlug();
        $citySlug->setSlug($slug);
        $citySlug->setCity($city);
        $this->entityManager->persist($citySlug);
        $city->setMainSlug($citySlug);

        $profile = new SocialNetworkProfile();
        $profile->setCity($city);
        $profile->setNetwork('twitter');
        $profile->setIdentifier('cm_test_' . substr(md5(uniqid('', true)), 0, 8));
        $profile->setCreatedAt(new \DateTime());
        $this->entityManager->persist($profile);

        $this->entityManager->flush();

        return [$city, $profile];
    }

    public function testDeleteProfileRemovesIt(): void
    {
        [$city, $profile] = $this->createProfile();
        $profileId = $profile->getId();

        $this->client->request('DELETE', '/api/' . $city->getMainSlugString() . '/socialnetwork-profiles/' . $profileId);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $this->assertNull($this->entityManager->getRepository(SocialNetworkProfile::class)->find($profileId));
    }
}
