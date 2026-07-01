<?php declare(strict_types=1);

namespace Tests\Controller\Api\SocialNetworkProfileApi;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * Tests der Social-Network-Delete-Endpunkte (Profil inkl. Feed-Items, Feed-Item).
 * Transaktions-isoliert.
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

    private function createProfileWithFeedItem(): array
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

        $feedItem = new SocialNetworkFeedItem();
        $feedItem->setSocialNetworkProfile($profile);
        $feedItem->setUniqueIdentifier('feed-' . substr(md5(uniqid('', true)), 0, 8));
        $feedItem->setTitle('Testmeldung');
        $feedItem->setText('Testinhalt');
        $feedItem->setDateTime(new \DateTime('2026-09-01 10:00:00'));
        $feedItem->setCreatedAt(new \DateTime());
        $this->entityManager->persist($feedItem);

        $this->entityManager->flush();

        return [$city, $profile, $feedItem];
    }

    public function testDeleteProfileRemovesItAndFeedItems(): void
    {
        [$city, $profile, $feedItem] = $this->createProfileWithFeedItem();
        $profileId = $profile->getId();
        $feedItemId = $feedItem->getId();

        $this->client->request('DELETE', '/api/' . $city->getMainSlugString() . '/socialnetwork-profiles/' . $profileId);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $this->assertNull($this->entityManager->getRepository(SocialNetworkProfile::class)->find($profileId));
        $this->assertNull($this->entityManager->getRepository(SocialNetworkFeedItem::class)->find($feedItemId));
    }

    public function testDeleteFeedItem(): void
    {
        [$city, , $feedItem] = $this->createProfileWithFeedItem();
        $feedItemId = $feedItem->getId();

        $this->client->request('DELETE', '/api/' . $city->getMainSlugString() . '/socialnetwork-feeditems/' . $feedItemId);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $this->assertNull($this->entityManager->getRepository(SocialNetworkFeedItem::class)->find($feedItemId));
    }
}
