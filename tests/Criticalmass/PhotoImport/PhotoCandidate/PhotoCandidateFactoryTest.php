<?php declare(strict_types=1);

namespace Tests\Criticalmass\PhotoImport\PhotoCandidate;

use App\Criticalmass\Image\ExifWrapper\ExifWrapperInterface;
use App\Criticalmass\PhotoImport\Normalizer\NormalizedImage;
use App\Criticalmass\PhotoImport\Normalizer\PhotoNormalizerInterface;
use App\Criticalmass\PhotoImport\PhotoCandidate\PhotoCandidateFactory;
use App\Entity\User;
use PHPExif\Exif;
use PHPUnit\Framework\TestCase;

class PhotoCandidateFactoryTest extends TestCase
{
    public function testUnsupportedFileTypeThrows(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->withTempFile('whatever', function (string $path): void {
            $this->factory($this->normalizer(), $this->exifWrapper(null))
                ->createFromUpload($path, 'notes.txt', $this->createMock(User::class));
        });
    }

    public function testBuildsCandidateWithExifDateAndCoordinates(): void
    {
        $exif = $this->createMock(Exif::class);
        $exif->method('getCreationDate')->willReturn(new \DateTime('2024-06-01 18:30:00'));
        $exif->method('getGPS')->willReturn('52.52,13.40');

        $normalizer = $this->normalizer(new NormalizedImage('JPEGBYTES', 'image/jpeg', 'jpg'));
        $factory = $this->factory($normalizer, $this->exifWrapper($exif));

        $parsed = $this->withTempFile('heic-bytes', fn (string $path) => $factory->createFromUpload($path, 'IMG_4711.heic', $this->createMock(User::class)));

        $candidate = $parsed->getCandidate();

        self::assertSame(sha1('heic-bytes'), $candidate->getFileHash());
        self::assertSame(sha1('heic-bytes') . '.jpg', $candidate->getStagedFilename(), 'HEIC is staged as the normalised JPEG.');
        self::assertSame('IMG_4711.heic', $candidate->getOriginalName());
        self::assertSame('image/jpeg', $candidate->getMimeType());
        self::assertSame('2024-06-01 18:30:00', $candidate->getExifCreationDate()?->format('Y-m-d H:i:s'));
        self::assertSame('2024-06-01', $candidate->getGalleryKey());
        self::assertEqualsWithDelta(52.52, $candidate->getLatitude(), 0.0001);
        self::assertEqualsWithDelta(13.40, $candidate->getLongitude(), 0.0001);
        self::assertSame('JPEGBYTES', $parsed->getImageBytes());
    }

    public function testBuildsCandidateWithoutExifMetadata(): void
    {
        $normalizer = $this->normalizer(new NormalizedImage('PNGBYTES', 'image/png', 'png'));
        $factory = $this->factory($normalizer, $this->exifWrapper(null));

        $parsed = $this->withTempFile('png-bytes', fn (string $path) => $factory->createFromUpload($path, 'screenshot.PNG', $this->createMock(User::class)));

        $candidate = $parsed->getCandidate();

        self::assertSame(sha1('png-bytes') . '.png', $candidate->getStagedFilename());
        self::assertNull($candidate->getExifCreationDate());
        self::assertNull($candidate->getGalleryKey(), 'A dateless photo has no gallery key.');
        self::assertNull($candidate->getLatitude());
        self::assertNull($candidate->getLongitude());
    }

    public function testNonStringGpsYieldsNoCoordinates(): void
    {
        $exif = $this->createMock(Exif::class);
        $exif->method('getCreationDate')->willReturn(new \DateTime('2024-06-01 18:30:00'));
        $exif->method('getGPS')->willReturn(false);

        $factory = $this->factory($this->normalizer(new NormalizedImage('JPEGBYTES', 'image/jpeg', 'jpg')), $this->exifWrapper($exif));

        $parsed = $this->withTempFile('jpeg-bytes', fn (string $path) => $factory->createFromUpload($path, 'photo.jpg', $this->createMock(User::class)));

        self::assertNull($parsed->getCandidate()->getLatitude());
        self::assertNull($parsed->getCandidate()->getLongitude());
        self::assertSame('2024-06-01 18:30:00', $parsed->getCandidate()->getExifCreationDate()?->format('Y-m-d H:i:s'));
    }

    private function factory(PhotoNormalizerInterface $normalizer, ExifWrapperInterface $exifWrapper): PhotoCandidateFactory
    {
        return new PhotoCandidateFactory($normalizer, $exifWrapper);
    }

    private function normalizer(?NormalizedImage $result = null): PhotoNormalizerInterface
    {
        $normalizer = $this->createMock(PhotoNormalizerInterface::class);

        if ($result !== null) {
            $normalizer->method('normalize')->willReturn($result);
        }

        return $normalizer;
    }

    private function exifWrapper(?Exif $exif): ExifWrapperInterface
    {
        $wrapper = $this->createMock(ExifWrapperInterface::class);
        $wrapper->method('readExifDataFromFile')->willReturn($exif);

        return $wrapper;
    }

    /**
     * @template T
     *
     * @param callable(string): T $callback
     *
     * @return T
     */
    private function withTempFile(string $contents, callable $callback): mixed
    {
        $path = tempnam(sys_get_temp_dir(), 'photo-factory-test-');
        self::assertIsString($path);
        file_put_contents($path, $contents);

        try {
            return $callback($path);
        } finally {
            @unlink($path);
        }
    }
}
