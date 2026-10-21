<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use App\Entity\Photo;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

/**
 * `photos` in den Ride-Details nennt nur aktive, nicht geloeschte Fotos (#1585).
 */
class RideApiPhotosTest extends AbstractApiControllerTestCase
{
    private function photoNamed(string $imageName): Photo
    {
        $photo = $this->entityManager->getRepository(Photo::class)->findOneBy(['imageName' => $imageName]);

        self::assertInstanceOf(Photo::class, $photo, sprintf('Die Fixtures brauchen das Foto %s.', $imageName));
        self::assertInstanceOf(Ride::class, $photo->getRide());

        return $photo;
    }

    /** @return list<int> */
    private function photoIdsFromRideDetails(Ride $ride): array
    {
        $this->client->request('GET', sprintf('/api/%s/%s', $ride->getCity()->getMainSlugString(), $ride->getDateTime()->format('Y-m-d')));

        self::assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        self::assertArrayHasKey('photos', $response);
        self::assertIsArray($response['photos']);

        return array_map(static fn (array $photo): int => $photo['id'], $response['photos']);
    }

    #[TestDox('GET /api/{citySlug}/{rideIdentifier} leaves a disabled photo out of photos')]
    public function testDisabledPhotoIsLeftOut(): void
    {
        $disabled = $this->photoNamed('berlin_ride_disabled.jpg');
        $active = $this->photoNamed('berlin_ride_001.jpg');

        self::assertSame($disabled->getRide(), $active->getRide());

        $ids = $this->photoIdsFromRideDetails($disabled->getRide());

        self::assertNotContains($disabled->getId(), $ids);
        self::assertContains($active->getId(), $ids);
    }

    #[TestDox('GET /api/{citySlug}/{rideIdentifier} leaves a deleted photo out of photos')]
    public function testDeletedPhotoIsLeftOut(): void
    {
        $deleted = $this->photoNamed('hamburg_ride_deleted.jpg');
        $active = $this->photoNamed('hamburg_ride_001.jpg');

        self::assertSame($deleted->getRide(), $active->getRide());

        $ids = $this->photoIdsFromRideDetails($deleted->getRide());

        self::assertNotContains($deleted->getId(), $ids);
        self::assertContains($active->getId(), $ids);
    }

    #[TestDox('A photo in photos carries the same fields as before')]
    public function testPhotoShapeIsUnchanged(): void
    {
        $active = $this->photoNamed('berlin_ride_001.jpg');

        $this->photoIdsFromRideDetails($active->getRide());

        $photos = array_values(array_filter($this->getJsonResponse()['photos'], static fn (array $photo): bool => $photo['id'] === $active->getId()));

        self::assertCount(1, $photos);
        self::assertEqualsCanonicalizing(['id', 'latitude', 'longitude'], array_keys($photos[0]));
    }
}
