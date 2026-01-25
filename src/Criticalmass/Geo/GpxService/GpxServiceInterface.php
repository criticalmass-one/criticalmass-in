<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\GpxService;

use App\Entity\Track;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;

interface GpxServiceInterface
{
    public function loadFromFile(string $path): GpxFile;

    public function loadFromTrack(Track $track): GpxFile;

    /**
     * @return Point[]
     */
    public function getPoints(Track $track): array;

    /**
     * @return Point[]
     */
    public function getPointsInRange(Track $track): array;

    public function findPointAtTime(Track $track, \DateTimeInterface $dateTime, ?\DateTimeZone $timeZone = null): ?Point;

    public function generatePolyline(Track $track): string;

    public function generateReducedPolyline(Track $track): string;

    public function calculateDistance(Track $track): float;

    public function generateLatLngList(Track $track, int $sampleWidth = 10): string;

    public function generateTimeLatLngList(Track $track, int $sampleWidth = 10): string;

    public function generateSimpleLatLngList(Track $track, int $sampleWidth = 10): string;

    public function getStartDateTime(Track $track): ?\DateTime;

    public function getEndDateTime(Track $track): ?\DateTime;

    public function shiftTimeAndSave(Track $track, \DateInterval $interval): void;

    /**
     * @param array<array{float, float}> $latLngData
     * @param array<float> $altitudeData
     * @param array<int> $timeData
     */
    public function createGpxFromStravaStream(
        array $latLngData,
        array $altitudeData,
        array $timeData,
        \DateTime $startDateTime
    ): GpxFile;

    public function toXmlString(GpxFile $gpxFile): string;
}
