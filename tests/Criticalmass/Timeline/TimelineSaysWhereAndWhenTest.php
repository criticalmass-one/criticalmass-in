<?php declare(strict_types=1);

namespace Tests\Criticalmass\Timeline;

use App\Entity\City;
use App\Entity\Ride;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Tests\Controller\AbstractControllerTestCase;

/**
 * Ein Timeline-Eintrag muss sagen, wo und wann.
 *
 * Der Titel einer Tour reicht dafuer nicht. "Critical Mass Offenbach
 * 28.08.2026" traegt Ort und Datum nur, weil jemand sie von Hand
 * hineingeschrieben hat; bei "KidicalMass" — so stand es am 08.09.2026 auf
 * der Startseite — verriet allein die Karte, dass Rosenheim gemeint war, und
 * wann die Fahrt startet, stand nirgends.
 */
class TimelineSaysWhereAndWhenTest extends AbstractControllerTestCase
{
    private const KARGER_TITEL = 'KidicalMass';

    /**
     * CachedTimeline baut seinen FilesystemAdapter an der Cache-Konfiguration
     * vorbei; ohne dieses Leeren beantwortet die Startseite den Aufruf aus
     * einem Bestand, der die eben angelegte Tour nicht kennt.
     */
    private function timelineZwischenspeicherLeeren(): void
    {
        (new FilesystemAdapter('criticalmass-timeline'))->clear();
    }

    /**
     * Der Text aller Angabenzeilen der Seite.
     *
     * Ueber alle, nicht nur die erste: Die Timeline zeigt auch die Touren der
     * Fixtures, und die eigene steht nicht zwangslaeufig oben.
     */
    private function alleAngaben(\Symfony\Component\DomCrawler\Crawler $crawler): string
    {
        return implode(' | ', $crawler->filter('.timeline-item-ride-meta')
            ->each(static fn (\Symfony\Component\DomCrawler\Crawler $zeile): string => $zeile->text()));
    }

    private function tourAnlegen(EntityManagerInterface $entityManager): Ride
    {
        $stadt = $entityManager->getRepository(City::class)->findOneBy([]);
        self::assertNotNull($stadt, 'Die Fixtures liefern mindestens eine Stadt.');
        self::assertNotNull($stadt->getCity(), 'Und die Stadt hat einen Namen.');

        $tour = new Ride();
        $tour->setCity($stadt);
        $tour->setTitle(self::KARGER_TITEL);
        // 16:30 UTC, damit sich eine Verschiebung in der Anzeige zeigen wuerde.
        $tour->setDateTime(new \DateTime('2026-09-18 16:30:00', new \DateTimeZone('UTC')));
        $tour->setCreatedAt(new \DateTime());
        $tour->setUpdatedAt(new \DateTime());
        $tour->setEnabled(true);

        $entityManager->persist($tour);
        $entityManager->flush();

        return $tour;
    }

    public function testTheTimelineNamesTheCity(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $tour = $this->tourAnlegen($entityManager);
        $this->timelineZwischenspeicherLeeren();

        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();

        self::assertGreaterThan(
            0,
            $crawler->filter('.timeline-item-ride-meta')->count(),
            'Jeder Toureneintrag traegt seine Angaben.'
        );
        self::assertStringContainsString(
            (string) $tour->getCity()?->getCity(),
            $this->alleAngaben($crawler),
            'Die Stadt steht dabei — der Titel allein verraet sie nicht.'
        );

        $entityManager->remove($tour);
        $entityManager->flush();
    }

    public function testTheTimelineNamesDateAndTime(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $tour = $this->tourAnlegen($entityManager);
        $this->timelineZwischenspeicherLeeren();

        $crawler = $client->request('GET', '/');
        $angaben = $this->alleAngaben($crawler);

        self::assertStringContainsString('18.09.2026', $angaben, 'Das Datum steht dabei.');
        self::assertMatchesRegularExpression('/\d{2}:\d{2} Uhr/', $angaben, 'Und die Uhrzeit.');

        $entityManager->remove($tour);
        $entityManager->flush();
    }

    /**
     * Die Uhrzeit wird in der Zeitzone der Stadt gezeigt, nicht als roher
     * Datenbankwert: In der Datenbank steht UTC, angezeigt gehoert Ortszeit.
     */
    public function testTheTimeIsNotTheRawUtcValue(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $tour = $this->tourAnlegen($entityManager);
        $stadt = $tour->getCity();
        $zone = $stadt?->getTimezone() ?? 'Europe/Berlin';

        $erwartet = (new \DateTimeImmutable('2026-09-18 16:30:00', new \DateTimeZone('UTC')))
            ->setTimezone(new \DateTimeZone($zone))
            ->format('H:i');

        $this->timelineZwischenspeicherLeeren();
        $crawler = $client->request('GET', '/');

        self::assertStringContainsString(
            $erwartet . ' Uhr',
            $this->alleAngaben($crawler),
            sprintf('16:30 UTC sind %s in %s.', $erwartet, $zone)
        );

        $entityManager->remove($tour);
        $entityManager->flush();
    }

    /**
     * Eine Tour ohne Stadt darf die Startseite nicht umwerfen — getCity() ist
     * nullbar, und die Timeline hat sich an genau solchen Luecken schon
     * einmal verschluckt.
     */
    public function testARideWithoutACityDoesNotBreakThePage(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $tour = new Ride();
        $tour->setTitle('Tour ohne Stadt');
        $tour->setDateTime(new \DateTime('+3 days'));
        $tour->setCreatedAt(new \DateTime());
        $tour->setUpdatedAt(new \DateTime());
        $tour->setEnabled(true);
        $entityManager->persist($tour);
        $entityManager->flush();

        $this->timelineZwischenspeicherLeeren();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful('Ohne Stadt bleibt die Seite stehen.');

        $entityManager->remove($tour);
        $entityManager->flush();
    }
}
