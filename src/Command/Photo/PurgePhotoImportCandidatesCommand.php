<?php declare(strict_types=1);

namespace App\Command\Photo;

use App\Entity\PhotoImportCandidate;
use App\Repository\PhotoImportCandidateRepository;
use Carbon\Carbon;
use Doctrine\Persistence\ManagerRegistry;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Housekeeping for the unified upload's photo candidates (mirrors the track variant,
 * #1387): removes rejected and expired, never-confirmed photo candidates together with
 * their staged files, and deletes orphaned files in the candidate storage that no
 * candidate references. Confirmed candidates are already removed on import.
 */
#[AsCommand(
    name: 'criticalmass:photos:purge-import-candidates',
    description: 'Remove rejected/expired upload photo candidates and orphaned staged files',
)]
class PurgePhotoImportCandidatesCommand extends Command
{
    private const DEFAULT_MAX_AGE_DAYS = 30;

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly FilesystemOperator $photoCandidateFilesystem,
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

        $io->section('Rejected & expired photo candidates');
        $removedCandidates = $this->purgeCandidates($io, $expiredBefore, $dryRun);

        $io->section('Orphaned staged files');
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
        /** @var PhotoImportCandidateRepository $repository */
        $repository = $this->registry->getRepository(PhotoImportCandidate::class);
        $candidates = $repository->findPurgeable($expiredBefore);

        if ($candidates === []) {
            $io->writeln('No candidates to purge.');

            return 0;
        }

        $manager = $this->registry->getManager();
        $count = 0;

        foreach ($candidates as $candidate) {
            $reason = $candidate->isRejected() ? 'rejected' : 'expired';
            $storagePath = $candidate->getStagedFilename();

            $io->writeln(sprintf(
                ' - #%d "%s" (%s, created %s)',
                (int) $candidate->getId(),
                (string) $candidate->getOriginalName(),
                $reason,
                $candidate->getCreatedAt()?->format('Y-m-d') ?? '?',
            ));

            if (!$dryRun) {
                if ($storagePath !== null && $this->photoCandidateFilesystem->fileExists($storagePath)) {
                    $this->photoCandidateFilesystem->delete($storagePath);
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
        /** @var PhotoImportCandidateRepository $repository */
        $repository = $this->registry->getRepository(PhotoImportCandidate::class);
        $referenced = array_flip($repository->findReferencedStagedFilenames());

        $count = 0;

        try {
            // Staged photos live at the storage root (filename = "<hash>.<ext>").
            foreach ($this->photoCandidateFilesystem->listContents('', false) as $item) {
                if (!$item->isFile()) {
                    continue;
                }

                $path = $item->path();

                if (isset($referenced[$path])) {
                    continue;
                }

                $io->writeln(sprintf(' - %s', $path));

                if (!$dryRun) {
                    $this->photoCandidateFilesystem->delete($path);
                }

                ++$count;
            }
        } catch (FilesystemException $exception) {
            $io->writeln('Candidate storage is not accessible yet.');

            return 0;
        }

        if ($count === 0) {
            $io->writeln('No orphaned files found.');
        }

        return $count;
    }
}
