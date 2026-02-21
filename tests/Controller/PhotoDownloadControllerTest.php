<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Photo;

class PhotoDownloadControllerTest extends AbstractControllerTestCase
{
    private function getFirstEnabledPhoto(): ?Photo
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        return $em->getRepository(Photo::class)
            ->createQueryBuilder('p')
            ->where('p.enabled = true')
            ->andWhere('p.deleted = false')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function testDownloadDeniedForAnonymousUser(): void
    {
        $client = static::createClient();

        $photo = $this->getFirstEnabledPhoto();
        $this->assertNotNull($photo, 'Photo fixture should exist');

        $client->request('GET', sprintf('/photo/%d/download', $photo->getId()));

        $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'Anonymous user should be redirected to login');
    }

    public function testDownloadDeniedForRegularUser(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $photo = $this->getFirstEnabledPhoto();
        $this->assertNotNull($photo, 'Photo fixture should exist');

        $client->request('GET', sprintf('/photo/%d/download', $photo->getId()));

        $this->assertEquals(403, $client->getResponse()->getStatusCode(), 'Regular user without ROLE_PHOTO_DOWNLOAD should get 403');
    }

    public function testDownloadAllowedForPhotoDownloadUser(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'photodownloader@criticalmass.in');

        $photo = $this->getFirstEnabledPhoto();
        $this->assertNotNull($photo, 'Photo fixture should exist');

        $client->request('GET', sprintf('/photo/%d/download', $photo->getId()));

        // The actual download might fail because the file doesn't exist in test environment,
        // but we should not get a 403 (access denied)
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertNotEquals(403, $statusCode, 'User with ROLE_PHOTO_DOWNLOAD should not get 403');
    }

    public function testDownloadAllowedForAdminUser(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'admin@criticalmass.in');

        $photo = $this->getFirstEnabledPhoto();
        $this->assertNotNull($photo, 'Photo fixture should exist');

        $client->request('GET', sprintf('/photo/%d/download', $photo->getId()));

        // Admin has ROLE_PHOTO_DOWNLOAD via hierarchy, should not get 403
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertNotEquals(403, $statusCode, 'Admin user should not get 403');
    }
}
