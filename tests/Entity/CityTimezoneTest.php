<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Die Zeitzonen der Staedte.
 *
 * Von 727 Staedten trugen 80 eine Zeitzone, die nicht zu ihren Koordinaten
 * passt — Los Angeles, Seattle und San Francisco standen auf Europe/Berlin,
 * neun Stunden daneben. Sichtbar wurde das in der Seitenleiste "Bald
 * unterwegs" und in der Timeline: Dort stand eine Uhrzeit, die vor Ort
 * niemand wiedererkennt.
 *
 * Korrigiert in Version20260908120000. Diese Tests bewachen, was daran
 * pruefbar ist, ohne eine Koordinaten-Datenbank mitzuschleppen.
 */
class CityTimezoneTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    /**
     * Jede Zeitzone der Migration muss eine gueltige Kennung sein.
     *
     * Ein Tippfehler — 'America/Los_Angles' statt 'America/Los_Angeles' —
     * wirft keinen Fehler beim Schreiben, sondern erst beim Anzeigen, und
     * dann irgendwo weit weg von hier.
     *
     * @return array<string, array{0: string}>
     */
    public static function zeitzonenDerMigration(): array
    {
        $quelle = file_get_contents(__DIR__ . '/../../migrations/Version20260908120000.php');
        self::assertNotFalse($quelle, 'Die Migration ist lesbar.');

        preg_match_all("/=> \['([^']+)', '([^']+)'\]/", $quelle, $treffer, PREG_SET_ORDER);
        self::assertNotEmpty($treffer, 'Die Migration enthaelt Zuordnungen.');

        $zonen = [];
        foreach ($treffer as [, $alt, $neu]) {
            $zonen[$alt] = [$alt];
            $zonen[$neu] = [$neu];
        }

        return $zonen;
    }

    #[DataProvider('zeitzonenDerMigration')]
    public function testEveryTimezoneInTheMigrationIsReal(string $kennung): void
    {
        self::assertContains(
            $kennung,
            \DateTimeZone::listIdentifiers(),
            sprintf('%s ist keine Zeitzone, die PHP kennt.', $kennung)
        );
    }

    /**
     * Und keine Stadt wird zweimal angefasst — sonst haengt das Ergebnis von
     * der Reihenfolge ab, und down() trifft nicht mehr, was up() tat.
     */
    public function testNoCityIsTouchedTwice(): void
    {
        $quelle = (string) file_get_contents(__DIR__ . '/../../migrations/Version20260908120000.php');

        preg_match_all("/^\s*(\d+) => \['/m", $quelle, $treffer);
        $ids = $treffer[1];

        self::assertNotEmpty($ids);
        self::assertSame(
            count($ids),
            count(array_unique($ids)),
            'Jede Stadt-ID kommt genau einmal vor.'
        );
    }

    /**
     * Alt und neu duerfen nie gleich sein — eine Anweisung, die nichts
     * aendert, ist bestenfalls Rauschen und schlimmstenfalls ein Zeichen
     * dafuer, dass etwas beim Erzeugen schiefging.
     */
    public function testNoEntryChangesNothing(): void
    {
        $quelle = (string) file_get_contents(__DIR__ . '/../../migrations/Version20260908120000.php');

        preg_match_all("/=> \['([^']+)', '([^']+)'\]/", $quelle, $treffer, PREG_SET_ORDER);

        foreach ($treffer as [$ganz, $alt, $neu]) {
            self::assertNotSame($alt, $neu, sprintf('%s aendert nichts.', $ganz));
        }
    }

    /**
     * Was in der Datenbank steht, muss PHP verstehen — sonst faellt es erst
     * beim Anzeigen auf, und dann in einem Template.
     */
    public function testEveryCityInTheDatabaseHasAUsableTimezone(): void
    {
        $bekannt = \DateTimeZone::listIdentifiers();
        $unbrauchbar = [];

        /** @var City $stadt */
        foreach ($this->entityManager->getRepository(City::class)->findAll() as $stadt) {
            $zone = $stadt->getTimezone();

            if (null === $zone || '' === $zone) {
                continue; // Ohne Angabe greift die Vorgabe der Anwendung.
            }

            if (!\in_array($zone, $bekannt, true)) {
                $unbrauchbar[] = sprintf('%s: %s', (string) $stadt->getCity(), $zone);
            }
        }

        self::assertSame([], $unbrauchbar, 'Keine Stadt traegt eine Zeitzone, die PHP nicht kennt.');
    }
}
