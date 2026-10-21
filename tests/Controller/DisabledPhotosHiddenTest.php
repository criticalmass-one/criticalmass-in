<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Repository\PhotoRepository;

/**
 * Ein deaktiviertes Foto darf nirgends mehr verlinkt oder mitgezaehlt sein, wo die
 * Einzelansicht es anschliessend mit 403 verweigert (#1585).
 */
class DisabledPhotosHiddenTest extends AbstractControllerTestCase
{
    /** Das deaktivierte Foto aus PhotoFixtures, haengt an der vergangenen Berliner Tour. */
    private const DISABLED_IMAGE_NAME = 'berlin_ride_disabled.jpg';

    private function disabledPhotoWithRide(): Photo
    {
        $photo = static::getContainer()->get('doctrine')->getRepository(Photo::class)->findOneBy(['imageName' => self::DISABLED_IMAGE_NAME]);

        self::assertInstanceOf(Photo::class, $photo, 'Die Fixtures brauchen das deaktivierte Foto.');
        self::assertFalse($photo->isEnabled());
        self::assertFalse($photo->isDeleted());
        self::assertInstanceOf(Ride::class, $photo->getRide());

        return $photo;
    }

    private function countVisiblePhotos(Ride $ride): int
    {
        return count(static::getContainer()->get('doctrine')->getRepository(Photo::class)->findBy([
            'ride' => $ride,
            'enabled' => true,
            'deleted' => false,
        ]));
    }

    public function testTheRepositoryLeavesDisabledPhotosOut(): void
    {
        static::bootKernel();
        $photo = $this->disabledPhotoWithRide();

        $photos = static::getContainer()->get(PhotoRepository::class)->findPhotosByRide($photo->getRide());

        self::assertNotContains($photo, $photos);
        self::assertNotEmpty(array_filter($photos, fn (Photo $p): bool => $p->isEnabled()), 'Aktive Fotos der Tour bleiben.');
    }

    public function testTheGalleryDoesNotLinkTheDisabledPhoto(): void
    {
        $client = static::createClient();
        $photo = $this->disabledPhotoWithRide();
        $router = static::getContainer()->get(ObjectRouterInterface::class);
        $photoPath = $router->generate($photo);

        $client->request('GET', $router->generate($photo->getRide(), 'caldera_criticalmass_photo_ride_list'));

        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists(sprintf('a[href="%s"]', $photoPath));
    }

    public function testTheRidePageCountsOnlyVisiblePhotos(): void
    {
        $client = static::createClient();
        $photo = $this->disabledPhotoWithRide();
        $ride = $photo->getRide();
        $router = static::getContainer()->get(ObjectRouterInterface::class);
        $visible = $this->countVisiblePhotos($ride);

        $crawler = $client->request('GET', $router->generate($ride));

        self::assertResponseIsSuccessful();
        $badge = $crawler->filter(sprintf('a[href="%s"] .badge', $router->generate($ride, 'caldera_criticalmass_photo_ride_list')));
        self::assertCount(1, $badge, 'Die Tourseite verlinkt die Galerie mit einem Zaehler.');
        self::assertSame($visible, (int) trim($badge->text()));
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
