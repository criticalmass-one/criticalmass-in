<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\Photo;
use App\Entity\Ride;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Was kein Bild ist, gehoert in keine Galerie.
 *
 * 17 Zeilen der Fototabelle tragen kein Bild: dreizehn Videos, die ein
 * frueherer Upload noch durchliess, und vier leere Dateien aus einem
 * missglueckten Upload. Liip kann daraus keine Miniaturansicht bauen und
 * wirft stattdessen — jeder Abruf einer solchen Vorschau endete in einem
 * 500er, seit dem 9. Juli 292 Mal.
 *
 * Version20260906100000 nimmt den Bestand aus dem Verkehr. Diese Tests
 * pruefen den Riegel davor: dass eine solche Zeile gar nicht erst in einer
 * Galerie auftaucht, auch wenn sie neu hineingeriete.
 */
class PhotosThatAreNotImagesTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private PhotoRepository $photoRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->photoRepository = static::getContainer()->get(PhotoRepository::class);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function nichtBilder(): array
    {
        return [
            'mp4' => ['video.mp4', 'video/mp4'],
            'quicktime' => ['video.mov', 'video/quicktime'],
            'avi' => ['video.avi', 'video/x-msvideo'],
            'leere Datei' => ['leer', 'application/x-empty'],
        ];
    }

    #[DataProvider('nichtBilder')]
    public function testTheRideGalleryLeavesItOut(string $dateiname, string $typ): void
    {
        $tour = $this->irgendeineTour();
        $vorher = count($this->photoRepository->findPhotosByRide($tour));

        $foto = $this->fotoAnlegen($tour, $dateiname, $typ);

        self::assertCount(
            $vorher,
            $this->photoRepository->findPhotosByRide($tour),
            sprintf('%s taucht in der Galerie der Tour nicht auf.', $typ)
        );

        $this->entityManager->remove($foto);
        $this->entityManager->flush();
    }

    public function testAnOrdinaryImageStillShows(): void
    {
        $tour = $this->irgendeineTour();
        $vorher = count($this->photoRepository->findPhotosByRide($tour));

        $foto = $this->fotoAnlegen($tour, 'foto.jpg', 'image/jpeg');

        self::assertCount(
            $vorher + 1,
            $this->photoRepository->findPhotosByRide($tour),
            'Ein echtes Bild kommt selbstverstaendlich weiterhin durch.'
        );

        $this->entityManager->remove($foto);
        $this->entityManager->flush();
    }

    /**
     * Alte Zeilen haben nie einen Typ bekommen. Sie gelten als Bild — sonst
     * verschwaende dieser Riegel den halben Bestand.
     */
    public function testAPhotoWithoutAMimeTypeCountsAsAnImage(): void
    {
        $tour = $this->irgendeineTour();
        $vorher = count($this->photoRepository->findPhotosByRide($tour));

        $foto = $this->fotoAnlegen($tour, 'alt.jpg', null);

        self::assertCount(
            $vorher + 1,
            $this->photoRepository->findPhotosByRide($tour),
            'Ohne Typangabe bleibt es sichtbar.'
        );

        $this->entityManager->remove($foto);
        $this->entityManager->flush();
    }

    /**
     * Und der zufaellige Ausschnitt fuer die Startseite darf gar nichts
     * anderes als Bilder liefern.
     */
    public function testTheRandomSelectionIsAllImages(): void
    {
        $tour = $this->irgendeineTour();
        $foto = $this->fotoAnlegen($tour, 'video.mp4', 'video/mp4');

        foreach ($this->photoRepository->findSomePhotos(100) as $treffer) {
            $typ = $treffer->getImageMimeType();

            self::assertTrue(
                null === $typ || str_starts_with($typ, 'image/'),
                sprintf('Der Ausschnitt enthaelt %s.', (string) $typ)
            );
        }

        $this->entityManager->remove($foto);
        $this->entityManager->flush();
    }

    private function irgendeineTour(): Ride
    {
        $tour = $this->entityManager->getRepository(Ride::class)->findOneBy([]);
        self::assertNotNull($tour, 'Die Fixtures liefern mindestens eine Tour.');

        return $tour;
    }

    private function fotoAnlegen(Ride $tour, string $dateiname, ?string $typ): Photo
    {
        $foto = new Photo();
        $foto->setRide($tour);
        $foto->setCity($tour->getCity());
        $foto->setImageName($dateiname);
        $foto->setImageMimeType($typ);
        $foto->setEnabled(true);
        $foto->setDeleted(false);

        $this->entityManager->persist($foto);
        $this->entityManager->flush();

        return $foto;
    }
}
