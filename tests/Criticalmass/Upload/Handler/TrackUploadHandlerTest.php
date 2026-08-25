<?php declare(strict_types=1);

namespace Tests\Criticalmass\Upload\Handler;

use App\Criticalmass\MassTrackImport\ProposalPersister\ProposalPersisterInterface;
use App\Criticalmass\MassTrackImport\TrackDecider\RideResult;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use App\Criticalmass\MassTrackImport\UploadedTrackCandidate\ParsedTrackUpload;
use App\Criticalmass\MassTrackImport\UploadedTrackCandidate\UploadedTrackCandidateFactory;
use App\Criticalmass\Upload\Handler\TrackUploadHandler;
use App\Criticalmass\Upload\UploadResult;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use App\Repository\TrackImportCandidateRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TrackUploadHandlerTest extends TestCase
{
    private MockObject&TrackDeciderInterface $decider;
    private MockObject&ProposalPersisterInterface $persister;
    private MockObject&FilesystemOperator $filesystem;
    private MockObject&TrackImportCandidateRepository $repository;
    private MockObject&ObjectManager $manager;
    private TrackImportCandidate $candidate;
    private TrackUploadHandler $handler;
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->candidate = (new TrackImportCandidate())->setFileHash('abc123')->setUser($this->user);

        $factory = $this->createMock(UploadedTrackCandidateFactory::class);
        $factory->method('createFromUpload')->willReturn(new ParsedTrackUpload($this->candidate, '<gpx/>'));

        $this->decider = $this->createMock(TrackDeciderInterface::class);
        $this->persister = $this->createMock(ProposalPersisterInterface::class);
        $this->filesystem = $this->createMock(FilesystemOperator::class);
        $this->repository = $this->createMock(TrackImportCandidateRepository::class);
        $this->manager = $this->createMock(ObjectManager::class);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(TrackImportCandidate::class)->willReturn($this->repository);
        $registry->method('getManager')->willReturn($this->manager);

        $this->handler = new TrackUploadHandler($factory, $this->decider, $this->persister, $this->filesystem, $registry);
    }

    #[Test]
    public function duplicateUploadIsReportedWithoutStoringAnything(): void
    {
        $this->repository->method('findOneBy')->with(['user' => $this->user, 'fileHash' => 'abc123'])->willReturn(new TrackImportCandidate());
        $this->filesystem->expects(self::never())->method('write');
        $this->decider->expects(self::never())->method('decide');
        $this->manager->expects(self::never())->method('persist');

        $result = $this->handler->handle('/tmp/ride.gpx', 'ride.gpx', $this->user);

        self::assertSame(UploadResult::KIND_TRACK, $result->kind);
        self::assertSame(UploadResult::STATUS_DUPLICATE, $result->status);
    }

    #[Test]
    public function matchedTrackIsStoredAndPersistedAsProposal(): void
    {
        $ride = (new Ride())->setTitle('Critical Mass Hamburg');
        $rideResult = new RideResult($ride, $this->candidate);

        $this->repository->method('findOneBy')->willReturn(null);
        $this->filesystem->expects(self::once())->method('write')->with('candidates/abc123.gpx', '<gpx/>');
        $this->decider->method('decide')->with($this->candidate)->willReturn($rideResult);
        $this->persister->expects(self::once())->method('persist')->with($rideResult)->willReturn($rideResult);
        $this->manager->expects(self::never())->method('persist');

        $result = $this->handler->handle('/tmp/ride.gpx', 'ride.gpx', $this->user);

        self::assertSame(UploadResult::STATUS_MATCHED, $result->status);
        self::assertStringContainsString('Critical Mass Hamburg', $result->message);
        self::assertSame('candidates/abc123.gpx', $this->candidate->getTrackFilename());
    }

    #[Test]
    public function unmatchedTrackIsParkedWithoutRide(): void
    {
        $this->candidate->setRide(new Ride());

        $this->repository->method('findOneBy')->willReturn(null);
        $this->decider->method('decide')->willReturn(null);
        $this->persister->expects(self::never())->method('persist');
        $this->manager->expects(self::once())->method('persist')->with($this->candidate);
        $this->manager->expects(self::once())->method('flush');

        $result = $this->handler->handle('/tmp/ride.gpx', 'ride.gpx', $this->user);

        self::assertSame(UploadResult::STATUS_PARKED, $result->status);
        self::assertNull($this->candidate->getRide());
    }
}
