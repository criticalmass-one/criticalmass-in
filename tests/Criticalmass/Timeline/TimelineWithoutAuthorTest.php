<?php declare(strict_types=1);

namespace Tests\Criticalmass\Timeline;

use App\Criticalmass\Timeline\Item\AbstractItem;
use App\Criticalmass\Timeline\Item\CityCreatedItem;
use App\Criticalmass\Timeline\Item\CityEditItem;
use App\Criticalmass\Timeline\Item\PhotoCommentItem;
use App\Criticalmass\Timeline\Item\RideCommentItem;
use App\Criticalmass\Timeline\Item\RideEditItem;
use App\Criticalmass\Timeline\Item\RideParticipationEstimateItem;
use App\Criticalmass\Timeline\Item\RidePhotoItem;
use App\Criticalmass\Timeline\Item\RideTrackItem;
use App\Criticalmass\Timeline\Item\ThreadItem;
use App\Criticalmass\Timeline\Item\ThreadPostItem;
use App\Entity\City;
use App\Entity\Ride;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Controller\AbstractControllerTestCase;

/**
 * Die Timeline muss Eintraege ohne Autor vertragen.
 *
 * Sie sind der Normalfall, nicht die Ausnahme: 24.464 der 26.820 Touren
 * stammen aus dem Tourengenerator und hatten nie jemanden, der sie angelegt
 * hat. Trotzdem verlangte AbstractItem::setUser() ein nicht-leeres User —
 * die Eigenschaft und der Getter waren seit jeher nullbar, nur der Setter
 * nicht.
 *
 * Getroffen hat das die Startseite, denn deren Timeline zeigt den letzten
 * Monat: Nach jedem Massenlauf des Generators fielen frisch erzeugte Touren
 * ohne Autor in dieses Fenster und die Seite antwortete mit einem 500er.
 * Am 1. September 2026, zwei Tage nach einem solchen Lauf, 304 Mal.
 */
class TimelineWithoutAuthorTest extends AbstractControllerTestCase
{
    /**
     * @return array<string, array{0: class-string<AbstractItem>}>
     */
    public static function itemKlassen(): array
    {
        return [
            'Stadt angelegt' => [CityCreatedItem::class],
            'Stadt bearbeitet' => [CityEditItem::class],
            'Foto kommentiert' => [PhotoCommentItem::class],
            'Tour kommentiert' => [RideCommentItem::class],
            'Tour bearbeitet' => [RideEditItem::class],
            'Teilnehmer geschaetzt' => [RideParticipationEstimateItem::class],
            'Fotos hochgeladen' => [RidePhotoItem::class],
            'Track hochgeladen' => [RideTrackItem::class],
            'Thema begonnen' => [ThreadItem::class],
            'Antwort geschrieben' => [ThreadPostItem::class],
        ];
    }

    /**
     * @param class-string<AbstractItem> $klasse
     */
    #[DataProvider('itemKlassen')]
    public function testAnItemAcceptsAMissingAuthor(string $klasse): void
    {
        $item = new $klasse();
        $item->setUser(null);

        self::assertNull($item->getUser());
    }

    /**
     * Der eigentliche Regressionstest: eine Tour ohne Autor, eben erst
     * geaendert — genau das, was der Generator hinterlaesst — und die
     * Startseite muss sie zeigen koennen.
     */
    public function testTheFrontpageSurvivesARideWithoutAnAuthor(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $tour = $this->tourOhneAutorAnlegen($entityManager);
        $this->timelineZwischenspeicherLeeren();

        $client->request('GET', '/');

        self::assertResponseIsSuccessful('Die Startseite haelt eine Tour ohne Autor aus.');

        $entityManager->remove($tour);
        $entityManager->flush();
    }

    /**
     * Und sie nennt dann keinen Urheber, sondern schreibt den Satz um.
     */
    public function testSuchARideIsReportedWithoutNamingAnAuthor(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $tour = $this->tourOhneAutorAnlegen($entityManager);
        $this->timelineZwischenspeicherLeeren();

        $crawler = $client->request('GET', '/');
        $inhalt = $crawler->filter('body')->text();

        self::assertStringContainsString(
            'Eine Tour wurde editiert',
            $inhalt,
            'Ohne Autor steht der Satz im Passiv.'
        );

        $entityManager->remove($tour);
        $entityManager->flush();
    }

    /**
     * CachedTimeline baut seinen FilesystemAdapter selbst, an der
     * Cache-Konfiguration vorbei; er ueberdauert damit auch Testlaeufe. Ohne
     * dieses Leeren beantwortet die Startseite den Aufruf aus einem Bestand,
     * der die eben angelegte Tour nicht kennt — der Test waere gruen, ohne
     * irgendetwas geprueft zu haben.
     */
    private function timelineZwischenspeicherLeeren(): void
    {
        (new FilesystemAdapter('criticalmass-timeline'))->clear();
    }

    private function tourOhneAutorAnlegen(EntityManagerInterface $entityManager): Ride
    {
        $stadt = $entityManager->getRepository(City::class)->findOneBy([]);
        self::assertNotNull($stadt, 'Die Fixtures liefern mindestens eine Stadt.');

        $tour = new Ride();
        $tour
            ->setCity($stadt)
            ->setTitle('Tour aus dem Generator')
            ->setDateTime(new \DateTime('+1 day'))
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setEnabled(true);

        // Kein setUser() — genau darum geht es.
        self::assertNull($tour->getUser());

        $entityManager->persist($tour);
        $entityManager->flush();

        return $tour;
    }
}
