<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Repository\PhotoRepository;

/**
 * Ein deaktiviertes Foto darf nirgends mehr verlinkt sein, wo die Einzelansicht
 * es anschließend mit 403 verweigert (#1585).
 */
class DisabledPhotosHiddenTest extends AbstractControllerTestCase
{
    private function disabledPhotoWithRide(): Photo
    {
        $photo = static::getContainer()->get('doctrine')->getRepository(Photo::class)->findOneBy([
            'enabled' => false,
            'deleted' => false,
        ]);

        self::assertInstanceOf(Photo::class, $photo, 'Die Fixtures brauchen ein deaktiviertes Foto.');
        self::assertInstanceOf(Ride::class, $photo->getRide());

        return $photo;
    }

    public function testTheRepositoryLeavesDisabledPhotosOut(): void
    {
        static::bootKernel();
        $photo = $this->disabledPhotoWithRide();

        $photos = static::getContainer()->get(PhotoRepository::class)->findPhotosByRide($photo->getRide());

        self::assertNotContains($photo, $photos);
        self::assertNotEmpty(array_filter($photos, fn (Photo $p): bool => $p->isEnabled()), 'Aktive Fotos der Tour bleiben.');
    }

    public function testGalleryAndRidePageDoNotLinkDisabledPhotos(): void
    {
        $client = static::createClient();
        $photo = $this->disabledPhotoWithRide();
        $router = static::getContainer()->get(ObjectRouterInterface::class);
        $photoPath = $router->generate($photo);

        foreach ([$router->generate($photo->getRide(), 'caldera_criticalmass_photo_ride_list'), $router->generate($photo->getRide())] as $path) {
            $client->request('GET', $path);

            self::assertResponseIsSuccessful($path);
            self::assertSelectorNotExists(sprintf('a[href="%s"]', $photoPath), sprintf('%s verlinkt das deaktivierte Foto.', $path));
        }
    }

    public function testTheDisabledPhotoItselfIsRefused(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');
        $photo = $this->disabledPhotoWithRide();

        $client->request('GET', static::getContainer()->get(ObjectRouterInterface::class)->generate($photo));

        self::assertResponseStatusCodeSame(403);
    }
}
