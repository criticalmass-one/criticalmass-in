<?php declare(strict_types=1);

namespace Tests\Criticalmass\PhotoImport\Review;

use App\Criticalmass\PhotoImport\PhotoDecider\PhotoDeciderInterface;
use App\Criticalmass\PhotoImport\Review\UploadReviewAssembler;
use App\Entity\PhotoImportCandidate;
use App\Entity\Ride;
use App\Entity\User;
use App\Repository\PhotoImportCandidateRepository;
use App\Repository\RideRepository;
use PHPUnit\Framework\TestCase;

class UploadReviewAssemblerTest extends TestCase
{
    public function testGroupsCandidatesByCaptureDate(): void
    {
        $candidates = [
            $this->candidate('2024-06-01 18:00:00'),
            $this->candidate('2024-06-01 18:05:00'),
            $this->candidate('2024-06-02 09:00:00'),
        ];

        $assembler = $this->assembler($candidates, [], null);

        $galleries = $assembler->photoGalleries($this->createMock(User::class));

        self::assertCount(2, $galleries);
        self::assertSame('2024-06-01', $galleries[0]->key);
        self::assertSame(2, $galleries[0]->count());
        self::assertSame('2024-06-02', $galleries[1]->key);
        self::assertSame(1, $galleries[1]->count());
    }

    public function testUndatedCandidatesFormAnUndatedGallery(): void
    {
        $assembler = $this->assembler([$this->candidate(null)], [], null);

        $galleries = $assembler->photoGalleries($this->createMock(User::class));

        self::assertCount(1, $galleries);
        self::assertSame(UploadReviewAssembler::UNDATED_KEY, $galleries[0]->key);
        self::assertFalse($galleries[0]->isDated());
        self::assertSame([], $galleries[0]->sameDateRides);
        self::assertFalse($galleries[0]->canBeAssigned());
    }

    public function testAttachesSuggestedRideAndSameDateRides(): void
    {
        $rideA = $this->createMock(Ride::class);
        $rideB = $this->createMock(Ride::class);

        $assembler = $this->assembler([$this->candidate('2024-06-01 18:00:00')], [$rideA, $rideB], $rideB);

        $galleries = $assembler->photoGalleries($this->createMock(User::class));

        self::assertSame([$rideA, $rideB], $galleries[0]->sameDateRides);
        self::assertSame($rideB, $galleries[0]->suggestedRide);
        self::assertTrue($galleries[0]->canBeAssigned());
    }

    public function testRidesOnDateIsEmptyForNullDate(): void
    {
        self::assertSame([], $this->assembler([], [], null)->ridesOnDate(null));
    }

    /**
     * @param list<PhotoImportCandidate> $candidates
     * @param list<Ride>                 $sameDateRides
     */
    private function assembler(array $candidates, array $sameDateRides, ?Ride $suggestedRide): UploadReviewAssembler
    {
        $photoRepository = $this->createMock(PhotoImportCandidateRepository::class);
        $photoRepository->method('findActiveForUser')->willReturn($candidates);

        $rideRepository = $this->createMock(RideRepository::class);
        $rideRepository->method('findByDate')->willReturn($sameDateRides);

        $decider = $this->createMock(PhotoDeciderInterface::class);
        $decider->method('decideForGallery')->willReturn($suggestedRide);

        return new UploadReviewAssembler($photoRepository, $rideRepository, $decider);
    }

    private function candidate(?string $exifDate): PhotoImportCandidate
    {
        return (new PhotoImportCandidate())
            ->setExifCreationDate($exifDate !== null ? new \DateTime($exifDate) : null);
    }
}
