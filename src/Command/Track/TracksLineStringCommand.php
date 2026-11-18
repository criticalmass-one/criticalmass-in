<?php declare(strict_types=1);

namespace App\Command\Track;

use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Entity\Track;
use App\Repository\TrackRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Fuellt die LineString-Geometrie der Tracks aus ihren GPX-Dateien (#1140).
 *
 * Die Migration legt die Spalte nur an — die Quelle ist die GPX-Datei, und die
 * kann SQL nicht lesen. Dieser Befehl holt die Punkte ueber denselben
 * GpxService, den auch die Polylinien nutzen, und schreibt sie als WKT.
 *
 * Idempotent: Er nimmt sich nur Tracks ohne Geometrie vor, laesst sich also
 * gefahrlos wiederholen und nach einem Abbruch fortsetzen.
 */
#[AsCommand(
    name: 'criticalmass:tracks:linestring',
    description: 'Fills the linestring geometry of tracks from their GPX files',
)]
class TracksLineStringCommand extends Command
{
    /**
     * Ein LineString braucht mindestens zwei Punkte — PostGIS lehnt alles
     * darunter ab. Ein Track mit einem einzigen Punkt ist ohnehin keine
     * Strecke.
     */
    private const MINDESTPUNKTE = 2;

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly GpxServiceInterface $gpxService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Hoechstens so viele Tracks bearbeiten')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Nur zeigen, was geschaehe')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Auch Tracks neu berechnen, die schon eine Geometrie haben');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $manager = $this->registry->getManager();

        $probelauf = (bool) $input->getOption('dry-run');
        $alle = (bool) $input->getOption('all');
        $limit = $input->getOption('limit');

        /** @var TrackRepository $repository */
        $repository = $this->registry->getRepository(Track::class);
        $builder = $repository->createQueryBuilder('t');

        if (!$alle) {
            $builder->where($builder->expr()->isNull('t.lineString'));
        }

        $builder->orderBy('t.id', 'ASC');

        if (null !== $limit) {
            $builder->setMaxResults((int) $limit);
        }

        /** @var Track[] $tracks */
        $tracks = $builder->getQuery()->getResult();

        if ([] === $tracks) {
            $io->success('Alle Tracks haben ihre Geometrie.');

            return Command::SUCCESS;
        }

        $io->note(sprintf('%d Tracks zu bearbeiten.%s', count($tracks), $probelauf ? ' (Probelauf)' : ''));

        $geschrieben = 0;
        $uebersprungen = 0;
        $gescheitert = 0;

        $io->progressStart(count($tracks));

        foreach ($tracks as $track) {
            $io->progressAdvance();

            try {
                $wkt = $this->wktAusGpx($track);
            } catch (\Throwable $fehler) {
                ++$gescheitert;
                $io->writeln('');
                $io->warning(sprintf('Track %d: %s', (int) $track->getId(), $fehler->getMessage()));

                continue;
            }

            if (null === $wkt) {
                ++$uebersprungen;

                continue;
            }

            if (!$probelauf) {
                $track->setLineString($wkt);
            }

            ++$geschrieben;

            // In Haeppchen schreiben, damit der Speicher bei 2.186 Dateien
            // nicht davonlaeuft.
            if (!$probelauf && 0 === $geschrieben % 50) {
                $manager->flush();
                $manager->clear();
            }
        }

        if (!$probelauf) {
            $manager->flush();
        }

        $io->progressFinish();

        $io->success(sprintf(
            '%d Geometrien %s, %d ohne genug Punkte uebersprungen, %d gescheitert.',
            $geschrieben,
            $probelauf ? 'waeren geschrieben worden' : 'geschrieben',
            $uebersprungen,
            $gescheitert
        ));

        return Command::SUCCESS;
    }

    /**
     * Die Punkte einer Fahrt — mit Zuschnitt, wo einer gesetzt ist.
     *
     * 36 der 2.186 Tracks haben `endPoint = 0`, was "kein Zuschnitt" heisst
     * und nicht "bis zum ersten Punkt". `getPointsInRange()` rechnet daraus
     * aber `array_slice($punkte, 0, 0 - 0 + 1)` — **genau einen Punkt**, und
     * damit keine Strecke. Diese Tracks blieben beim ersten Lauf allesamt
     * liegen, und der Befehl meldete sie als "ohne genug Punkte", was formal
     * stimmte und in die Irre fuehrte.
     *
     * @return array<int, \phpGPX\Models\Point>
     */
    private function punkte(Track $track): array
    {
        $von = (int) $track->getStartPoint();
        $bis = (int) $track->getEndPoint();

        if ($bis > $von) {
            return $this->gpxService->getPointsInRange($track);
        }

        return $this->gpxService->getPoints($track);
    }

    /**
     * Baut das WKT aus den Punkten der GPX-Datei.
     *
     * In WKT steht die Laenge vor der Breite — anders herum als in jeder
     * Beschriftung dieser Anwendung, und die Falle, die bei den
     * Punktgeometrien schon zweimal Aufmerksamkeit gekostet hat.
     */
    private function wktAusGpx(Track $track): ?string
    {
        $punkte = $this->punkte($track);

        $paare = [];

        foreach ($punkte as $punkt) {
            if (null === $punkt->latitude || null === $punkt->longitude) {
                continue;
            }

            $paare[] = sprintf('%.8F %.8F', $punkt->longitude, $punkt->latitude);
        }

        if (count($paare) < self::MINDESTPUNKTE) {
            return null;
        }

        return sprintf('SRID=4326;LINESTRING(%s)', implode(', ', $paare));
    }
}
