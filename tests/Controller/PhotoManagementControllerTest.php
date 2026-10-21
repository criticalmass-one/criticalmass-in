<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Photo;
use Doctrine\Persistence\ManagerRegistry;

class PhotoManagementControllerTest extends AbstractControllerTestCase
{
    private const IMAGE_NAME = 'hamburg_ride_001.jpg';

    /** Immer frisch aus der Datenbank — der Client bootet den Kernel je Anfrage neu. */
    private function photo(): Photo
    {
        /** @var ManagerRegistry $doctrine */
        $doctrine = static::getContainer()->get('doctrine');
        $doctrine->getManager()->clear();

        $photo = $doctrine->getRepository(Photo::class)->findOneBy(['imageName' => self::IMAGE_NAME]);

        self::assertInstanceOf(Photo::class, $photo);

        return $photo;
    }

    public function testTheOwnerTogglesThePhotoOutOfAndBackIntoTheGallery(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $photo = $this->photo();
        self::assertTrue($photo->isEnabled());

        $router = static::getContainer()->get(ObjectRouterInterface::class);
        $photoPath = $router->generate($photo);
        $galleryPath = $router->generate($photo->getRide(), 'caldera_criticalmass_photo_ride_list');
        $togglePath = $router->generate($photo, 'caldera_criticalmass_photo_toggle');

        try {
            $client->request('GET', $togglePath, server: ['HTTP_REFERER' => $galleryPath]);

            self::assertResponseRedirects($galleryPath);
            self::assertFalse($this->photo()->isEnabled());

            $client->request('GET', $galleryPath);

            self::assertResponseIsSuccessful();
            self::assertSelectorNotExists(sprintf('a[href="%s"]', $photoPath));

            $client->request('GET', $togglePath, server: ['HTTP_REFERER' => $galleryPath]);

            self::assertResponseRedirects($galleryPath);
            self::assertTrue($this->photo()->isEnabled());

            $client->request('GET', $galleryPath);

            self::assertResponseIsSuccessful();
            self::assertSelectorExists(sprintf('a[href="%s"]', $photoPath));
        } finally {
            static::getContainer()->get('doctrine')->getConnection()
                ->executeStatement('UPDATE photo SET enabled = true WHERE imagename = :name', ['name' => self::IMAGE_NAME]);
        }
    }

    public function testSomeoneElseMayNotToggleThePhoto(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'cyclist@criticalmass.in');

        $photo = $this->photo();

        $client->request('GET', static::getContainer()->get(ObjectRouterInterface::class)->generate($photo, 'caldera_criticalmass_photo_toggle'));

        self::assertResponseStatusCodeSame(403);
        self::assertTrue($this->photo()->isEnabled());
    }
}
