<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Photo;
use App\Entity\Ride;

/**
 * /{citySlug}/galleries zaehlt je Tour nur die Fotos, die die Galerie auch zeigt (#1585).
 */
class CityGalleryListControllerTest extends AbstractControllerTestCase
{
    public function testTheCounterLeavesDisabledPhotosOut(): void
    {
        $client = static::createClient();
        $repository = static::getContainer()->get('doctrine')->getRepository(Photo::class);

        $disabled = $repository->findOneBy(['imageName' => 'berlin_ride_disabled.jpg']);
        self::assertInstanceOf(Photo::class, $disabled);
        $ride = $disabled->getRide();
        self::assertInstanceOf(Ride::class, $ride);

        $visible = count($repository->findBy(['ride' => $ride, 'enabled' => true, 'deleted' => false]));
        $undeleted = count($repository->findBy(['ride' => $ride, 'deleted' => false]));
        self::assertGreaterThan($visible, $undeleted, 'Die Tour braucht ein deaktiviertes Foto, sonst prueft der Test nichts.');

        $router = static::getContainer()->get(ObjectRouterInterface::class);
        $galleryPath = $router->generate($ride, 'caldera_criticalmass_photo_ride_list');

        $crawler = $client->request('GET', $router->generate($ride->getCity(), 'caldera_criticalmass_city_listgalleries'));

        self::assertResponseIsSuccessful();
        $badge = $crawler->filter(sprintf('a[href="%s"]', $galleryPath));
        self::assertCount(1, $badge);
        self::assertSame($visible, (int) trim($badge->text()));
    }
}
