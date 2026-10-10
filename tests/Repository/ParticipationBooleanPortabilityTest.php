<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\Participation;
use App\Entity\User;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Boolesche Spalten muessen gegen einen Wahrheitswert verglichen werden.
 *
 * `$builder->expr()->eq('p.goingYes', true)` sieht aus wie ein Vergleich mit
 * einem Wert, ist aber keiner: Das zweite Argument ist ein DQL-Bruchstueck.
 * Aus dem PHP-true wurde die Zeichenkette "1", und die Abfrage lautete
 * "goingYes = 1".
 *
 * MySQL nahm das hin. PostgreSQL nicht:
 *
 *     SQLSTATE[42883]: Undefined function: operator does not exist:
 *     boolean = integer
 *
 * Seit dem Umzug am 05.09.2026 endete /profile/participation/list damit im
 * 500er -- eine Seite hinter der Anmeldung, weshalb es fuenf Tage dauerte, bis
 * es jemandem auffiel. Die Testsuite lief die ganze Zeit gruen, weil kein Test
 * diese drei Zweige je betrat.
 *
 * Die Tests hier rufen jede Kombination auf. Sie pruefen bewusst kein
 * Ergebnis: Was zurueckkommt, haengt an den Fixtures. Es geht darum, dass die
 * Datenbank die Abfrage ueberhaupt annimmt.
 */
class ParticipationBooleanPortabilityTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    private function repository(): ParticipationRepository
    {
        $repository = static::getContainer()->get(ParticipationRepository::class);
        self::assertInstanceOf(ParticipationRepository::class, $repository);

        return $repository;
    }

    /**
     * Irgendein Konto, das ueberhaupt schon einmal teilgenommen hat -- an einem
     * Konto ohne Teilnahmen liefe die Abfrage zwar auch, aber der Vergleich
     * stuende dann in einem Zweig, den die Datenbank womoeglich wegoptimiert.
     */
    private function teilnehmendesKonto(): User
    {
        $teilnahme = $this->entityManager->getRepository(Participation::class)->findOneBy([]);
        self::assertInstanceOf(Participation::class, $teilnahme, 'Die Fixtures kennen keine Teilnahme.');

        $konto = $teilnahme->getUser();
        self::assertInstanceOf(User::class, $konto);

        return $konto;
    }

    public function testFilteringByYesIsAcceptedByTheDatabase(): void
    {
        $ergebnis = $this->repository()->findByUser($this->teilnehmendesKonto(), true);

        self::assertContainsOnlyInstancesOf(Participation::class, $ergebnis);
    }

    public function testFilteringByMaybeIsAcceptedByTheDatabase(): void
    {
        $ergebnis = $this->repository()->findByUser($this->teilnehmendesKonto(), false, true);

        self::assertContainsOnlyInstancesOf(Participation::class, $ergebnis);
    }

    public function testFilteringByNoIsAcceptedByTheDatabase(): void
    {
        $ergebnis = $this->repository()->findByUser($this->teilnehmendesKonto(), false, false, true);

        self::assertContainsOnlyInstancesOf(Participation::class, $ergebnis);
    }

    /**
     * Die Profilseite fragt alle drei nacheinander ab, auf demselben Builder-
     * Muster. Der Reihe nach, damit auch die Kombination durchlaeuft.
     */
    public function testTheProfilePageAsksForAllThreeInARow(): void
    {
        $konto = $this->teilnehmendesKonto();
        $repository = $this->repository();

        self::assertContainsOnlyInstancesOf(Participation::class, $repository->findByUser($konto, true));
        self::assertContainsOnlyInstancesOf(Participation::class, $repository->findByUser($konto, false, true));
        self::assertContainsOnlyInstancesOf(Participation::class, $repository->findByUser($konto, false, false, true));
    }

    /**
     * Und ohne jeden Filter -- der Zweig, der schon vorher lief.
     */
    public function testFilteringByNothingStillWorks(): void
    {
        self::assertContainsOnlyInstancesOf(Participation::class, $this->repository()->findByUser($this->teilnehmendesKonto()));
    }
}
