<?php declare(strict_types=1);

namespace Tests\Criticalmass\Upload\Handler;

use App\Criticalmass\PhotoImport\PhotoCandidate\ParsedPhotoUpload;
use App\Criticalmass\PhotoImport\PhotoCandidate\PhotoCandidateFactory;
use App\Criticalmass\Upload\Handler\PhotoUploadHandler;
use App\Criticalmass\Upload\UploadResult;
use App\Entity\PhotoImportCandidate;
use App\Entity\User;
use App\Repository\PhotoImportCandidateRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PhotoUploadHandlerTest extends TestCase
{
    private MockObject&FilesystemOperator $filesystem;
    private MockObject&PhotoImportCandidateRepository $repository;
    private MockObject&ObjectManager $manager;
    private PhotoImportCandidate $candidate;
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->candidate = (new PhotoImportCandidate())->setUser($this->user)->setFileHash('deadbeef')->setStagedFilename('deadbeef.jpg');

        $this->filesystem = $this->createMock(FilesystemOperator::class);
        $this->repository = $this->createMock(PhotoImportCandidateRepository::class);
        $this->manager = $this->createMock(ObjectManager::class);
    }

    private function handler(string $imageBytes): PhotoUploadHandler
    {
        $factory = $this->createMock(PhotoCandidateFactory::class);
        $factory->method('createFromUpload')->willReturn(new ParsedPhotoUpload($this->candidate, $imageBytes));

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($this->manager);

        return new PhotoUploadHandler($factory, $this->filesystem, $this->repository, $registry);
    }

    private function onePixelPng(): string
    {
        return (string) base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==', true);
    }

    #[Test]
    public function undecodableBytesAreRejectedBeforeAnyLookup(): void
    {
        $this->repository->expects(self::never())->method('findOneByUserAndFileHash');
        $this->filesystem->expects(self::never())->method('write');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('kein gültiges Bild');

        $this->handler('not an image')->handle('/tmp/x.jpg', 'x.jpg', $this->user);
    }

    #[Test]
    public function duplicateImageIsReportedWithoutStaging(): void
    {
        $this->repository->method('findOneByUserAndFileHash')->with($this->user, 'deadbeef')->willReturn(new PhotoImportCandidate());
        $this->filesystem->expects(self::never())->method('write');
        $this->manager->expects(self::never())->method('persist');

        $result = $this->handler($this->onePixelPng())->handle('/tmp/x.png', 'x.png', $this->user);

        self::assertSame(UploadResult::KIND_PHOTO, $result->kind);
        self::assertSame(UploadResult::STATUS_DUPLICATE, $result->status);
    }

    #[Test]
    public function newImageIsStagedAndPersisted(): void
    {
        $bytes = $this->onePixelPng();

        $this->repository->method('findOneByUserAndFileHash')->willReturn(null);
        $this->filesystem->expects(self::once())->method('write')->with('deadbeef.jpg', $bytes);
        $this->manager->expects(self::once())->method('persist')->with($this->candidate);
        $this->manager->expects(self::once())->method('flush');

        $result = $this->handler($bytes)->handle('/tmp/x.png', 'x.png', $this->user);

        self::assertSame(UploadResult::STATUS_STAGED, $result->status);
    }
}
