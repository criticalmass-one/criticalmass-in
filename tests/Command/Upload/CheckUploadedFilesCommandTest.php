<?php declare(strict_types=1);

namespace Tests\Command\Upload;

use App\Command\Upload\CheckUploadedFilesCommand;
use App\Command\Upload\UploadMappingInfo;
use App\Command\Upload\UploadMappingResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CheckUploadedFilesCommandTest extends TestCase
{
    private MockObject&UploadMappingResolverInterface $mappingResolver;
    private MockObject&EntityManagerInterface $entityManager;
    private MockObject&FilesystemOperator $filesystem;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->mappingResolver = $this->createMock(UploadMappingResolverInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->filesystem = $this->createMock(FilesystemOperator::class);

        $command = new CheckUploadedFilesCommand(
            $this->entityManager,
            $this->mappingResolver,
            ['track_file' => $this->filesystem],
        );

        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($application->find('criticalmass:uploads:check'));
    }

    public function testDetectsMissingFiles(): void
    {
        $this->setupMappingForTrack();
        $this->setupDatabaseResults([
            ['id' => 1, 'trackFilename' => 'existing.gpx'],
            ['id' => 2, 'trackFilename' => 'missing.gpx'],
        ]);
        $this->setupFilesystemContents(['existing.gpx']);

        $this->commandTester->execute(['entity' => 'Track']);

        $display = $this->commandTester->getDisplay();
        $this->assertStringContainsString('MISS', $display);
        $this->assertStringContainsString('missing.gpx', $display);
        $this->assertStringContainsString('ID: 2', $display);
    }

    public function testDetectsOrphanedFiles(): void
    {
        $this->setupMappingForTrack();
        $this->setupDatabaseResults([
            ['id' => 1, 'trackFilename' => 'existing.gpx'],
        ]);
        $this->setupFilesystemContents(['existing.gpx', 'orphan.gpx']);

        $this->commandTester->execute(['entity' => 'Track']);

        $display = $this->commandTester->getDisplay();
        $this->assertStringContainsString('ORPHAN', $display);
        $this->assertStringContainsString('orphan.gpx', $display);
    }

    public function testConsistentStateShowsSuccess(): void
    {
        $this->setupMappingForTrack();
        $this->setupDatabaseResults([
            ['id' => 1, 'trackFilename' => 'file.gpx'],
        ]);
        $this->setupFilesystemContents(['file.gpx']);

        $this->commandTester->execute(['entity' => 'Track']);

        $display = $this->commandTester->getDisplay();
        $this->assertStringContainsString('All consistent', $display);
        $this->assertStringNotContainsString('MISS', $display);
        $this->assertStringNotContainsString('ORPHAN', $display);
    }

    public function testNullFilenamesAreIgnored(): void
    {
        $this->setupMappingForTrack();
        $this->setupDatabaseResults([
            ['id' => 1, 'trackFilename' => 'file.gpx'],
            ['id' => 2, 'trackFilename' => null],
        ]);
        $this->setupFilesystemContents(['file.gpx']);

        $this->commandTester->execute(['entity' => 'Track']);

        $display = $this->commandTester->getDisplay();
        $this->assertStringContainsString('All consistent', $display);
    }

    public function testUnknownEntityReturnsFailure(): void
    {
        $this->commandTester->execute(['entity' => 'NonExistentEntity']);

        $this->assertSame(1, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('not found', $this->commandTester->getDisplay());
    }

    private function setupMappingForTrack(): void
    {
        $this->mappingResolver
            ->method('resolve')
            ->willReturn([new UploadMappingInfo('trackFilename', 'track_file')]);
    }

    /** @param list<array<string, mixed>> $results */
    private function setupDatabaseResults(array $results): void
    {
        $query = $this->createMock(Query::class);
        $query->method('getArrayResult')->willReturn($results);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->method('select')->willReturnSelf();
        $qb->method('from')->willReturnSelf();
        $qb->method('getQuery')->willReturn($query);

        $this->entityManager->method('createQueryBuilder')->willReturn($qb);
    }

    /** @param list<string> $filenames */
    private function setupFilesystemContents(array $filenames): void
    {
        $items = array_map(
            fn(string $name) => new FileAttributes($name),
            $filenames,
        );

        $this->filesystem
            ->method('listContents')
            ->willReturn(new DirectoryListing(new \ArrayIterator($items)));
    }
}
