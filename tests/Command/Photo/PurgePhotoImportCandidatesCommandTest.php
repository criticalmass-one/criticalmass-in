<?php declare(strict_types=1);

namespace Tests\Command\Photo;

use App\Command\Photo\PurgePhotoImportCandidatesCommand;
use App\Entity\PhotoImportCandidate;
use App\Repository\PhotoImportCandidateRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PurgePhotoImportCandidatesCommandTest extends TestCase
{
    private MockObject&ManagerRegistry $registry;
    private MockObject&PhotoImportCandidateRepository $repository;
    private MockObject&ObjectManager $manager;
    private MockObject&FilesystemOperator $filesystem;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = $this->createMock(PhotoImportCandidateRepository::class);
        $this->manager = $this->createMock(ObjectManager::class);
        $this->filesystem = $this->createMock(FilesystemOperator::class);

        $this->registry->method('getRepository')->willReturn($this->repository);
        $this->registry->method('getManager')->willReturn($this->manager);

        $command = new PurgePhotoImportCandidatesCommand($this->registry, $this->filesystem);

        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($application->find('criticalmass:photos:purge-import-candidates'));
    }

    private function makeCandidate(bool $rejected, string $file): PhotoImportCandidate
    {
        return (new PhotoImportCandidate())
            ->setRejected($rejected)
            ->setOriginalName($file)
            ->setStagedFilename($file);
    }

    public function testDryRunDeletesNothing(): void
    {
        $this->repository->method('findPurgeable')->willReturn([$this->makeCandidate(true, 'a.jpg')]);
        $this->repository->method('findReferencedStagedFilenames')->willReturn(['keep.jpg']);
        $this->filesystem->method('listContents')->willReturn(new DirectoryListing(new \ArrayIterator([
            new FileAttributes('keep.jpg'),
            new FileAttributes('orphan.jpg'),
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
        $this->repository->method('findPurgeable')->willReturn([
            $this->makeCandidate(true, 'a.jpg'),
            $this->makeCandidate(false, 'b.jpg'),
        ]);
        $this->repository->method('findReferencedStagedFilenames')->willReturn(['a.jpg', 'b.jpg']);

        $this->filesystem->method('fileExists')->willReturn(true);
        $this->filesystem->method('listContents')->willReturn(new DirectoryListing(new \ArrayIterator([
            new FileAttributes('a.jpg'),
            new FileAttributes('b.jpg'),
            new FileAttributes('orphan.jpg'),
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

    public function testNothingToPurgeWithEmptyStorage(): void
    {
        $this->repository->method('findPurgeable')->willReturn([]);
        $this->repository->method('findReferencedStagedFilenames')->willReturn([]);
        $this->filesystem->method('listContents')->willReturn(new DirectoryListing(new \ArrayIterator([])));

        $this->commandTester->execute([]);

        $display = $this->commandTester->getDisplay();
        $this->assertStringContainsString('No candidates to purge', $display);
        $this->assertStringContainsString('No orphaned files found', $display);
        $this->assertStringContainsString('Removed 0 candidate(s) and 0 orphaned file(s)', $display);
        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testUnreadableStorageIsHandledGracefully(): void
    {
        $this->repository->method('findPurgeable')->willReturn([]);
        $this->repository->method('findReferencedStagedFilenames')->willReturn([]);
        $this->filesystem->method('listContents')->willThrowException(
            new class('storage unavailable') extends \RuntimeException implements FilesystemException {},
        );

        $this->commandTester->execute([]);

        $display = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Candidate storage is not accessible yet', $display);
        $this->assertStringContainsString('Removed 0 candidate(s) and 0 orphaned file(s)', $display);
        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}
