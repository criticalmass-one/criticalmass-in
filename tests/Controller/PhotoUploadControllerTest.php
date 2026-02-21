<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Photo;
use App\Entity\Ride;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoUploadControllerTest extends AbstractControllerTestCase
{
    private function getPastRideForCity(string $citySlug): ?Ride
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        return $em->getRepository(Ride::class)
            ->createQueryBuilder('r')
            ->join('r.city', 'c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :citySlug')
            ->andWhere('r.dateTime < :now')
            ->setParameter('citySlug', $citySlug)
            ->setParameter('now', new \DateTime())
            ->orderBy('r.dateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function buildRideUrl(Ride $ride): string
    {
        return sprintf('/%s/%s', $ride->getCity()->getMainSlugString(), $ride->getDateTime()->format('Y-m-d'));
    }

    private function generateTestImage(): string
    {
        $image = imagecreatetruecolor(200, 150);
        $bgColor = imagecolorallocate($image, 100, 150, 200);
        imagefill($image, 0, 0, $bgColor);

        $tmpFile = tempnam(sys_get_temp_dir(), 'photo_test_');
        imagejpeg($image, $tmpFile, 90);
        imagedestroy($image);

        return $tmpFile;
    }

    private function countPhotosForRide(int $rideId): int
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        return (int) $em->getRepository(Photo::class)->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.ride = :rideId')
            ->andWhere('p.deleted = :deleted')
            ->setParameter('rideId', $rideId)
            ->setParameter('deleted', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getNewestPhotoForRide(int $rideId): ?Photo
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        return $em->getRepository(Photo::class)->createQueryBuilder('p')
            ->where('p.ride = :rideId')
            ->setParameter('rideId', $rideId)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function uploadPhoto(KernelBrowser $client, Ride $ride): void
    {
        $tmpFile = $this->generateTestImage();
        $uploadedFile = new UploadedFile($tmpFile, 'test_photo.jpg', 'image/jpeg', null, true);

        // Set CSRF token in session
        $client->request('GET', $this->buildRideUrl($ride) . '/addphoto');
        $session = $client->getRequest()->getSession();
        $session->set('_csrf/photo_upload', 'test_token');
        $session->save();

        $client->request('POST', $this->buildRideUrl($ride) . '/addphoto', ['_token' => 'test_token'], ['file' => $uploadedFile]);
    }

    public function testPhotoUploadPageAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride, 'Past Hamburg ride fixture should exist');

        $client->request('GET', $this->buildRideUrl($ride) . '/addphoto');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPhotoUploadRedirectsWithoutLogin(): void
    {
        $client = static::createClient();

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride, 'Past Hamburg ride fixture should exist');

        $client->request('GET', $this->buildRideUrl($ride) . '/addphoto');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testPhotoUploadReturnsSuccessResponse(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride, 'Past Hamburg ride fixture should exist');

        $tmpFile = $this->generateTestImage();

        try {
            $uploadedFile = new UploadedFile($tmpFile, 'test_photo.jpg', 'image/jpeg', null, true);

            // Set CSRF token in session
            $client->request('GET', $this->buildRideUrl($ride) . '/addphoto');
            $session = $client->getRequest()->getSession();
            $session->set('_csrf/photo_upload', 'test_token');
            $session->save();

            $client->request('POST', $this->buildRideUrl($ride) . '/addphoto', ['_token' => 'test_token'], ['file' => $uploadedFile]);

            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertEquals('Success', $client->getResponse()->getContent());
        } finally {
            @unlink($tmpFile);
        }
    }

    public function testPhotoUploadWithoutLoginRedirects(): void
    {
        $client = static::createClient();

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride, 'Past Hamburg ride fixture should exist');

        $tmpFile = $this->generateTestImage();

        try {
            $uploadedFile = new UploadedFile($tmpFile, 'test_photo.jpg', 'image/jpeg', null, true);
            $client->request('POST', $this->buildRideUrl($ride) . '/addphoto', [], ['file' => $uploadedFile]);

            $this->assertEquals(302, $client->getResponse()->getStatusCode());
        } finally {
            @unlink($tmpFile);
        }
    }

    public function testPhotoExistsInDatabaseAfterUpload(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('berlin');
        $this->assertNotNull($ride, 'Past Berlin ride fixture should exist');

        $photoCountBefore = $this->countPhotosForRide($ride->getId());

        $this->uploadPhoto($client, $ride);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $photoCountAfter = $this->countPhotosForRide($ride->getId());
        $this->assertEquals($photoCountBefore + 1, $photoCountAfter, 'One new photo should exist after upload');
    }

    public function testPhotoAssociatedWithCorrectRideAndUser(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride, 'Past Hamburg ride fixture should exist');
        $rideId = $ride->getId();

        $this->uploadPhoto($client, $ride);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $photo = $this->getNewestPhotoForRide($rideId);

        $this->assertNotNull($photo, 'Photo should exist in database');
        $this->assertNotNull($photo->getUser(), 'Photo should be associated with a user');
        $this->assertEquals('testuser@criticalmass.in', $photo->getUser()->getEmail());
        $this->assertNotNull($photo->getCity(), 'Photo should be associated with a city');
        $this->assertNotNull($photo->getImageName(), 'Photo should have an image name');
    }

    public function testMultiplePhotosUpload(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('berlin');
        $this->assertNotNull($ride, 'Past Berlin ride fixture should exist');

        $photoCountBefore = $this->countPhotosForRide($ride->getId());

        $this->uploadPhoto($client, $ride);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->uploadPhoto($client, $ride);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $photoCountAfter = $this->countPhotosForRide($ride->getId());
        $this->assertEquals($photoCountBefore + 2, $photoCountAfter, 'Two new photos should exist after uploads');
    }

    public function testGalleryListPageAccessible(): void
    {
        $client = static::createClient();

        $ride = $this->getPastRideForCity('hamburg');
        $this->assertNotNull($ride, 'Past Hamburg ride fixture should exist');

        $client->request('GET', $this->buildRideUrl($ride) . '/listphotos');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPhotoCountIncreasesOnRidePage(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $ride = $this->getPastRideForCity('berlin');
        $this->assertNotNull($ride, 'Past Berlin ride fixture should exist');

        $photoCountBefore = $this->countPhotosForRide($ride->getId());

        $this->uploadPhoto($client, $ride);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $photoCountAfter = $this->countPhotosForRide($ride->getId());
        $this->assertEquals($photoCountBefore + 1, $photoCountAfter, 'Photo count should increase by 1 after upload');

        // Verify ride page still loads correctly after photo upload
        $client->request('GET', $this->buildRideUrl($ride));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
