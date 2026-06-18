<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\Normalizer;

/**
 * Passes JPEG/PNG/WebP/GIF through unchanged and converts HEIC/HEIF to JPEG via
 * ImageMagick (preserving the EXIF profile, so date/GPS survive for matching and
 * gallery display). HEIC support requires ImageMagick with the libheif delegate
 * (present in production via the LiipImagine imagick driver); when it is missing
 * the conversion fails closed with a clear message instead of a 500.
 */
final class ImagickPhotoNormalizer implements PhotoNormalizerInterface
{
    private const PASSTHROUGH = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
    ];

    private const HEIC = ['heic', 'heif'];

    public function normalize(string $filePath, string $originalName): NormalizedImage
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (isset(self::PASSTHROUGH[$extension])) {
            $bytes = @file_get_contents($filePath);

            if ($bytes === false) {
                throw new \RuntimeException('Die Bilddatei konnte nicht gelesen werden.');
            }

            return new NormalizedImage($bytes, self::PASSTHROUGH[$extension], $extension === 'jpeg' ? 'jpg' : $extension);
        }

        if (\in_array($extension, self::HEIC, true)) {
            return $this->convertHeicToJpeg($filePath);
        }

        throw new \RuntimeException(sprintf('Das Bildformat „.%s“ wird nicht unterstützt.', $extension));
    }

    private function convertHeicToJpeg(string $filePath): NormalizedImage
    {
        if (!\extension_loaded('imagick')) {
            throw new \RuntimeException('HEIC/HEIF wird auf diesem Server nicht unterstützt (ImageMagick nicht verfügbar).');
        }

        $imagick = new \Imagick();

        if ([] === $imagick->queryFormats('HEIC')) {
            throw new \RuntimeException('HEIC/HEIF wird auf diesem Server nicht unterstützt (kein HEIF-Delegate).');
        }

        try {
            $imagick->readImage($filePath);
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(90);
            // EXIF-Profil NICHT strippen → Datum/GPS bleiben erhalten.
            $bytes = $imagick->getImageBlob();
        } catch (\ImagickException $exception) {
            throw new \RuntimeException(sprintf('Die HEIC-Datei konnte nicht konvertiert werden: %s', $exception->getMessage()), 0, $exception);
        } finally {
            $imagick->clear();
        }

        return new NormalizedImage($bytes, 'image/jpeg', 'jpg');
    }
}
