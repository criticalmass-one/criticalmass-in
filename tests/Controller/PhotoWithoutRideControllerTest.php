<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Die Fotoseite muss ein Foto ohne Tour zeigen koennen.
 *
 * 139 Fotos haengen an keiner Tour, eines an keinem Nutzer. Der Controller
 * weiss das laengst — er prueft `if ($ride && ...)`, bevor er den Track sucht,
 * und reicht dann `null` an das Template weiter. Nur das Template rechnete
 * nicht damit und rief `object_path(null)` auf, was seit dem 20. Juli in
 * einem 500er endete, gleichmaessig ueber die Wochen verteilt und zuletzt
 * heute wieder.
 */
class PhotoWithoutRideControllerTest extends AbstractControllerTestCase
{
    public function testThePageWorksForAPhotoWithoutARide(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $foto = $this->fotoAnlegen($entityManager, mitNutzer: true);

        $client->request('GET', sprintf('/photo/%d', $foto->getId()));

        self::assertResponseIsSuccessful('Ein Foto ohne Tour hat trotzdem eine Seite.');

        $entityManager->remove($foto);
        $entityManager->flush();
    }

    /**
     * Ohne Tour faellt der Tourenzweig der Brotkrumen weg — die Stadt bleibt.
     */
    public function testTheBreadcrumbFallsBackToTheCity(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $foto = $this->fotoAnlegen($entityManager, mitNutzer: true);

        $crawler = $client->request('GET', sprintf('/photo/%d', $foto->getId()));
        $brotkrumen = $crawler->filter('.breadcrumb')->text();

        self::assertStringContainsString(
            $foto->getCity()->getCity(),
            $brotkrumen,
            'Die Stadt steht weiterhin in den Brotkrumen.'
        );
        self::assertStringContainsString(
            sprintf('Foto %d', $foto->getId()),
            $brotkrumen,
            'Und das Foto selbst als aktiver Eintrag.'
        );

        $entityManager->remove($foto);
        $entityManager->flush();
    }

    /**
     * Und auch ohne Hochladenden: Ohne ihn gibt es niemanden, der das
     * Verschleiern der Galerien eingestellt haben koennte.
     */
    public function testThePageWorksForAPhotoWithoutAUser(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $foto = $this->fotoAnlegen($entityManager, mitNutzer: false);

        $client->request('GET', sprintf('/photo/%d', $foto->getId()));

        self::assertResponseIsSuccessful('Ein Foto ohne Hochladenden hat trotzdem eine Seite.');

        $entityManager->remove($foto);
        $entityManager->flush();
    }

    private function fotoAnlegen(EntityManagerInterface $entityManager, bool $mitNutzer): Photo
    {
        $stadt = $entityManager->getRepository(City::class)->findOneBy([]);
        self::assertNotNull($stadt, 'Die Fixtures liefern mindestens eine Stadt.');

        $foto = new Photo();
        $foto
            ->setCity($stadt)
            ->setImageName('foto-ohne-tour.jpg')
            ->setEnabled(true)
            ->setDeleted(false);

        if ($mitNutzer) {
            $foto->setUser($entityManager->getRepository(User::class)->findOneBy([]));
        }

        // Kein setRide() — genau darum geht es.
        self::assertNull($foto->getRide());

        $entityManager->persist($foto);
        $entityManager->flush();

        return $foto;
    }
}
