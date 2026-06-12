<?php declare(strict_types=1);

namespace App\Command\Track;

use App\Entity\TrackImportCandidate;
use App\Repository\TrackImportCandidateRepository;
use Carbon\Carbon;
use Doctrine\Persistence\ManagerRegistry;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Housekeeping for bulk-upload track candidates (#1387): removes rejected and expired,
 * never-confirmed upload candidates together with their stored files, and deletes orphaned
 * files in the candidate storage that no candidate references. Strava candidates (which
 * carry no uploaded file) are left untouched.
 */
#[AsCommand(
    name: 'criticalmass:tracks:purge-import-candidates',
    description: 'Remove rejected/expired upload track candidates and orphaned candidate files',
)]
class PurgeTrackImportCandidatesCommand extends Command
{
    private const CANDIDATE_DIRECTORY = 'candidates';
    private const DEFAULT_MAX_AGE_DAYS = 30;

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly FilesystemOperator $trackFilesystem,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('max-age', null, InputOption::VALUE_REQUIRED, 'Maximum age in days for never-confirmed candidates', (string) self::DEFAULT_MAX_AGE_DAYS)
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Show what would be deleted without deleting anything');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dryRun = (bool) $input->getOption('dry-run');
        $maxAgeDays = max(0, (int) $input->getOption('max-age'));
        $expiredBefore = Carbon::now()->subDays($maxAgeDays);

        if ($dryRun) {
            $io->note('Dry run — nothing will be deleted.');
        }

        $io->section('Rejected & expired upload candidates');
        $removedCandidates = $this->purgeCandidates($io, $expiredBefore, $dryRun);

        $io->section('Orphaned candidate files');
        $removedFiles = $this->purgeOrphanedFiles($io, $dryRun);

        $io->success(sprintf(
            '%s %d candidate(s) and %d orphaned file(s).',
            $dryRun ? 'Would remove' : 'Removed',
            $removedCandidates,
            $removedFiles,
        ));

        return Command::SUCCESS;
    }

    private function purgeCandidates(SymfonyStyle $io, \DateTimeInterface $expiredBefore, bool $dryRun): int
    {
        /** @var TrackImportCandidateRepository $repository */
        $repository = $this->registry->getRepository(TrackImportCandidate::class);
        $candidates = $repository->findPurgeableUploadCandidates($expiredBefore);

        if ($candidates === []) {
            $io->writeln('No candidates to purge.');

            return 0;
        }

        $manager = $this->registry->getManager();
        $count = 0;

        foreach ($candidates as $candidate) {
            $reason = $candidate->isRejected() ? 'rejected' : 'expired';
            $storagePath = $candidate->getTrackFilename();

            $io->writeln(sprintf(
                ' - #%d "%s" (%s, created %s)',
                $candidate->getId(),
                (string) $candidate->getOriginalName(),
                $reason,
                $candidate->getCreatedAt()->format('Y-m-d'),
            ));

            if (!$dryRun) {
                if ($storagePath !== null && $this->trackFilesystem->fileExists($storagePath)) {
                    $this->trackFilesystem->delete($storagePath);
                }

                $manager->remove($candidate);
            }

            ++$count;
        }

        if (!$dryRun) {
            $manager->flush();
        }

        return $count;
    }

    private function purgeOrphanedFiles(SymfonyStyle $io, bool $dryRun): int
    {
        if (!$this->trackFilesystem->directoryExists(self::CANDIDATE_DIRECTORY)) {
            $io->writeln('Candidate storage directory does not exist yet.');

            return 0;
        }

        /** @var TrackImportCandidateRepository $repository */
        $repository = $this->registry->getRepository(TrackImportCandidate::class);
        $referenced = array_flip($repository->findReferencedUploadFilenames());

        $count = 0;

        foreach ($this->trackFilesystem->listContents(self::CANDIDATE_DIRECTORY) as $item) {
            if (!$item->isFile()) {
                continue;
            }

            $path = $item->path();

            if (isset($referenced[$path])) {
                continue;
            }

            $io->writeln(sprintf(' - %s', $path));

            if (!$dryRun) {
                $this->trackFilesystem->delete($path);
            }

            ++$count;
        }

        if ($count === 0) {
            $io->writeln('No orphaned files found.');
        }

        return $count;
    }
}
