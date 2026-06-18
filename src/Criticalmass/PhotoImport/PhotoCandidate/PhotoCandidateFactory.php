<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\PhotoCandidate;

use App\Criticalmass\Image\ExifWrapper\ExifWrapperInterface;
use App\Criticalmass\PhotoImport\Normalizer\PhotoNormalizerInterface;
use App\Entity\PhotoImportCandidate;
use App\Entity\User;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Builds a PhotoImportCandidate from an uploaded image file.
 *
 * HEIC/HEIF are normalised to JPEG (see PhotoNormalizer), so the staged file is
 * always a web-friendly format. EXIF is read from the *normalised* bytes, which
 * keeps the capture date and GPS coordinates available even for HEIC originals —
 * these drive the gallery grouping and the ride matching (PhotoDecider).
 */
class PhotoCandidateFactory
{
    private const SUPPORTED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'heic', 'heif'];

    public function __construct(
        private readonly PhotoNormalizerInterface $normalizer,
        private readonly ExifWrapperInterface $exifWrapper,
    ) {
    }

    /**
     * @throws \RuntimeException if the file type is unsupported or cannot be read
     */
    public function createFromUpload(string $filePath, string $originalName, User $user): ParsedPhotoUpload
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, self::SUPPORTED_EXTENSIONS, true)) {
            throw new \RuntimeException(sprintf('Das Dateiformat „.%s“ wird nicht unterstützt — bitte lade nur Bilddateien hoch.', $extension));
        }

        $normalized = $this->normalizer->normalize($filePath, $originalName);

        $exifDate = null;
        $latitude = null;
        $longitude = null;

        $this->withTemporaryFile($normalized->bytes, function (string $tmpPath) use (&$exifDate, &$latitude, &$longitude): void {
            $exif = $this->exifWrapper->readExifDataFromFile($tmpPath);

            if ($exif === null) {
                return;
            }

            $creationDate = $exif->getCreationDate();
            if ($creationDate instanceof \DateTimeInterface) {
                // Wall-clock capture time as recorded by the camera; kept as-is so a
                // photo groups and matches on the day it was actually taken.
                $exifDate = \DateTime::createFromInterface($creationDate);
            }

            $coords = $this->extractCoordinates($exif->getGPS());
            if ($coords !== null) {
                [$latitude, $longitude] = $coords;
            }
        });

        $hash = sha1_file($filePath);
        $fileHash = $hash !== false ? $hash : sha1($originalName);

        $candidate = new PhotoImportCandidate();
        $candidate
            ->setUser($user)
            ->setFileHash($fileHash)
            ->setStagedFilename(sprintf('%s.%s', $fileHash, $normalized->extension))
            ->setOriginalName($originalName)
            ->setMimeType($normalized->mimeType)
            ->setExifCreationDate($exifDate)
            ->setLatitude($latitude)
            ->setLongitude($longitude);

        return new ParsedPhotoUpload($candidate, $normalized->bytes);
    }

    /**
     * @param callable(string): void $callback
     */
    private function withTemporaryFile(string $bytes, callable $callback): void
    {
        $filesystem = new Filesystem();
        $tmpPath = $filesystem->tempnam(sys_get_temp_dir(), 'photo-exif-');

        try {
            $filesystem->dumpFile($tmpPath, $bytes);
            $callback($tmpPath);
        } finally {
            $filesystem->remove($tmpPath);
        }
    }

    /**
     * Mirrors PhotoGps: php-exif returns the GPS position as a "lat,lon" string.
     *
     * @return array{0: float, 1: float}|null
     */
    private function extractCoordinates(mixed $gps): ?array
    {
        if (!is_string($gps) || !str_contains($gps, ',')) {
            return null;
        }

        [$lat, $lon] = explode(',', $gps);

        return [(float) $lat, (float) $lon];
    }
}
