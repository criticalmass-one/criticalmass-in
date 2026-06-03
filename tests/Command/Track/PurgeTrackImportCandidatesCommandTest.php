<?php declare(strict_types=1);

namespace Tests\Command\Track;

use App\Command\Track\PurgeTrackImportCandidatesCommand;
use App\Entity\TrackImportCandidate;
use App\Repository\TrackImportCandidateRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PurgeTrackImportCandidatesCommandTest extends TestCase
{
    private MockObject&ManagerRegistry $registry;
    private MockObject&TrackImportCandidateRepository $repository;
    private MockObject&ObjectManager $manager;
    private MockObject&FilesystemOperator $filesystem;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = $this->createMock(TrackImportCandidateRepository::class);
        $this->manager = $this->createMock(ObjectManager::class);
        $this->filesystem = $this->createMock(FilesystemOperator::class);

        $this->registry->method('getRepository')->willReturn($this->repository);
        $this->registry->method('getManager')->willReturn($this->manager);

        $command = new PurgeTrackImportCandidatesCommand($this->registry, $this->filesystem);

        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($application->find('criticalmass:tracks:purge-import-candidates'));
    }

    private function makeCandidate(int $id, bool $rejected, ?string $file): TrackImportCandidate
    {
        $candidate = new TrackImportCandidate();
        $candidate
            ->setId($id)
            ->setSource(TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD)
            ->setOriginalName(sprintf('file-%d.gpx', $id))
            ->setRejected($rejected)
            ->setTrackFilename($file);

        return $candidate;
    }

    public function testDryRunDeletesNothing(): void
    {
        $this->repository->method('findPurgeableUploadCandidates')->willReturn([
            $this->makeCandidate(1, true, 'candidates/a.gpx'),
        ]);
        $this->filesystem->method('directoryExists')->willReturn(true);
        $this->repository->method('findReferencedUploadFilenames')->willReturn(['candidates/keep.gpx']);
        $this->filesystem->method('listContents')->willReturn(new DirectoryListing(new \ArrayIterator([
            new FileAttributes('candidates/keep.gpx'),
            new FileAttributes('candidates/orphan.gpx'),
        ])));

        $this->manager->expects($this->never())->method('remove');
        $this->manager->expects($this->never())->method('flush');
        $this->filesystem->expects($this->never())->method('delete');

        $this->commandTester->execute(['--dry-run' => true]);

        $display = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Dry run', $display);
        $this->assertStringContainsString('Would remove 1 candidate(s) and 1 orphaned file(s)', $display);
        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testPurgeRemovesCandidatesAndOrphanedFiles(): void
    {
        $rejected = $this->makeCandidate(1, true, 'candidates/a.gpx');
        $expired = $this->makeCandidate(2, false, 'candidates/b.gpx');

        $this->repository->method('findPurgeableUploadCandidates')->willReturn([$rejected, $expired]);
        $this->repository->method('findReferencedUploadFilenames')->willReturn(['candidates/a.gpx', 'candidates/b.gpx']);

        $this->filesystem->method('directoryExists')->willReturn(true);
        $this->filesystem->method('fileExists')->willReturn(true);
        $this->filesystem->method('listContents')->willReturn(new DirectoryListing(new \ArrayIterator([
            new FileAttributes('candidates/a.gpx'),
            new FileAttributes('candidates/b.gpx'),
            new FileAttributes('candidates/orphan.gpx'),
        ])));

        // Two candidate files + one orphan should be deleted.
        $this->filesystem->expects($this->exactly(3))->method('delete');
        $this->manager->expects($this->exactly(2))->method('remove');
        $this->manager->expects($this->atLeastOnce())->method('flush');

        $this->commandTester->execute([]);

        $display = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Removed 2 candidate(s) and 1 orphaned file(s)', $display);
        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testNoCandidatesAndNoStorageDirectory(): void
    {
        $this->repository->method('findPurgeableUploadCandidates')->willReturn([]);
        $this->filesystem->method('directoryExists')->willReturn(false);

        $this->commandTester->execute([]);

        $display = $this->commandTester->getDisplay();
        $this->assertStringContainsString('No candidates to purge', $display);
        $this->assertStringContainsString('Removed 0 candidate(s) and 0 orphaned file(s)', $display);
        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}
