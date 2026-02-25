<?php declare(strict_types=1);

namespace App\Command\Upload;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'criticalmass:uploads:check',
    description: 'Check consistency between database upload references and filesystem',
)]
class CheckUploadedFilesCommand extends Command
{
    private const ENTITY_LIST = [
        'Photo',
        'Track',
        'User',
        'City',
        'Ride',
        'FrontpageTeaser',
    ];

    /** @param array<string, FilesystemOperator> $filesystems */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UploadMappingResolverInterface $mappingResolver,
        private readonly array $filesystems,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('entity', InputArgument::OPTIONAL, 'Entity class name (e.g. Track). If omitted, all entities are checked.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $entityArgument = $input->getArgument('entity');

        $entityNames = $entityArgument ? [(string) $entityArgument] : self::ENTITY_LIST;

        $totalMissing = 0;
        $totalOrphan = 0;

        foreach ($entityNames as $entityName) {
            $fqcn = sprintf('App\\Entity\\%s', $entityName);

            if (!class_exists($fqcn)) {
                $io->error(sprintf('Entity class "%s" not found.', $fqcn));
                return Command::FAILURE;
            }

            $mappings = $this->mappingResolver->resolve($fqcn);

            if (!$mappings) {
                $io->warning(sprintf('No upload mappings found for %s.', $entityName));
                continue;
            }

            foreach ($mappings as $mappingInfo) {
                $filenameProperty = $mappingInfo->filenameProperty;
                $mappingName = $mappingInfo->mappingName;

                $io->section(sprintf('%s — %s (%s)', $entityName, $filenameProperty, $mappingName));

                $filesystem = $this->filesystems[$mappingName] ?? null;

                if (!$filesystem) {
                    $io->error(sprintf('No filesystem configured for mapping "%s".', $mappingName));
                    return Command::FAILURE;
                }

                $dbEntries = $this->loadDatabaseFilenames($fqcn, $filenameProperty);
                $fsFilenames = $this->loadFilesystemFilenames($filesystem);

                $dbFilenames = array_map(fn(array $row) => $row[$filenameProperty], $dbEntries);
                $dbFilenames = array_filter($dbFilenames);

                $dbIdMap = [];
                foreach ($dbEntries as $row) {
                    if ($row[$filenameProperty]) {
                        $dbIdMap[$row[$filenameProperty]] = $row['id'];
                    }
                }

                $missing = array_diff($dbFilenames, $fsFilenames);
                $orphan = array_diff($fsFilenames, $dbFilenames);

                $totalMissing += count($missing);
                $totalOrphan += count($orphan);

                if (count($missing) === 0 && count($orphan) === 0) {
                    $io->success(sprintf('All consistent — %d files in DB, %d files on disk.', count($dbFilenames), count($fsFilenames)));
                    continue;
                }

                $table = new Table($output);
                $table->setHeaders(['Status', 'Filename', 'Details']);

                foreach ($missing as $filename) {
                    $table->addRow(['MISS', $filename, sprintf('ID: %s', $dbIdMap[$filename] ?? '?')]);
                }

                foreach ($orphan as $filename) {
                    $table->addRow(['ORPHAN', $filename, '']);
                }

                $table->render();
                $io->newLine();
            }
        }

        $io->newLine();

        if ($totalMissing === 0 && $totalOrphan === 0) {
            $io->success('All uploads are consistent.');
        } else {
            $io->warning(sprintf('Found %d missing file(s) and %d orphaned file(s).', $totalMissing, $totalOrphan));
        }

        return Command::SUCCESS;
    }

    /** @return array<int, array<string, mixed>> */
    private function loadDatabaseFilenames(string $fqcn, string $filenameProperty): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(sprintf('e.id, e.%s', $filenameProperty))
            ->from($fqcn, 'e');

        return $qb->getQuery()->getArrayResult();
    }

    /** @return list<string> */
    private function loadFilesystemFilenames(FilesystemOperator $filesystem): array
    {
        $filenames = [];

        foreach ($filesystem->listContents('', false) as $item) {
            if ($item->isFile()) {
                $filenames[] = basename($item->path());
            }
        }

        return $filenames;
    }
}
