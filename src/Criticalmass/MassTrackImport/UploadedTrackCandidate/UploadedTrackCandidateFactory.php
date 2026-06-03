<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\UploadedTrackCandidate;

use App\Criticalmass\Geo\Coord\Coord;
use App\Criticalmass\Geo\FitService\FitToGpxConverterInterface;
use App\Criticalmass\Geo\GeoUtil\GeoUtil;
use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;

/**
 * Builds a quellen-agnostic TrackImportCandidate from an uploaded GPX or FIT file.
 *
 * FIT is normalised to GPX (see FitToGpxConverter), so the stored file is always GPX
 * and the rest of the import pipeline (TrackDecider / FileTrackImporter) is unaffected
 * by the original upload format.
 */
class UploadedTrackCandidateFactory
{
    private const MINIMUM_POINTS = 2;

    public function __construct(
        private readonly GpxServiceInterface $gpxService,
        private readonly FitToGpxConverterInterface $fitToGpxConverter,
    ) {
    }

    /**
     * @throws \RuntimeException if the file type is unsupported or the track is unusable
     */
    public function createFromUpload(string $filePath, string $originalName, User $user): ParsedTrackUpload
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $gpxFile = match ($extension) {
            'fit' => $this->fitToGpxConverter->convertFileToGpxFile($filePath),
            'gpx' => $this->gpxService->loadFromFile($filePath),
            default => throw new \RuntimeException(sprintf('Unsupported file type ".%s" — only .gpx and .fit are allowed.', $extension)),
        };

        $points = $this->extractUsablePoints($gpxFile);

        $firstPoint = $points[0];
        $lastPoint = $points[count($points) - 1];

        $startTime = $firstPoint->time;
        $endTime = $lastPoint->time;

        if ($startTime === null || $endTime === null) {
            throw new \RuntimeException('The uploaded track has no timestamps and cannot be matched to a ride.');
        }

        $hash = sha1_file($filePath);

        $candidate = new TrackImportCandidate();
        $candidate
            ->setUser($user)
            ->setSource(TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD)
            ->setType('Ride')
            ->setName($originalName)
            ->setOriginalName($originalName)
            ->setFileHash($hash !== false ? $hash : sha1($originalName))
            ->setStartDateTime(\DateTime::createFromInterface($startTime))
            ->setStartCoord(new Coord((float) $firstPoint->latitude, (float) $firstPoint->longitude))
            ->setEndCoord(new Coord((float) $lastPoint->latitude, (float) $lastPoint->longitude))
            ->setElapsedTime($endTime->getTimestamp() - $startTime->getTimestamp())
            ->setDistance($this->calculateDistance($points))
            ->setPolyline($this->encodePolyline($points));

        return new ParsedTrackUpload($candidate, $gpxFile->toXML()->saveXML());
    }

    /**
     * @return list<Point>
     */
    private function extractUsablePoints(GpxFile $gpxFile): array
    {
        if (count($gpxFile->tracks) === 0) {
            throw new \RuntimeException('The uploaded file contains no track.');
        }

        $points = array_values(array_filter(
            $gpxFile->tracks[0]->getPoints(),
            static fn (Point $point): bool => $point->latitude !== null && $point->longitude !== null,
        ));

        if (count($points) < self::MINIMUM_POINTS) {
            throw new \RuntimeException('The uploaded track has no usable GPS coordinates.');
        }

        return $points;
    }

    /**
     * @param list<Point> $points
     */
    private function calculateDistance(array $points): float
    {
        $distance = 0.0;
        $previous = $points[0];

        foreach ($points as $point) {
            $distance += GeoUtil::calculateDistanceFromCoords(
                (float) $previous->latitude,
                (float) $previous->longitude,
                (float) $point->latitude,
                (float) $point->longitude,
            );
            $previous = $point;
        }

        return $distance;
    }

    /**
     * @param list<Point> $points
     */
    private function encodePolyline(array $points): string
    {
        $coordinates = [];

        foreach ($points as $point) {
            $coordinates[] = [(float) $point->latitude, (float) $point->longitude];
        }

        return \Polyline::Encode($coordinates);
    }
}
