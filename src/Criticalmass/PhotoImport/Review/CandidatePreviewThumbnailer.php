<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\Review;

/**
 * Produces small JPEG thumbnails of staged candidate images for the review page.
 *
 * Staged photos live outside the web root and are served owner-only, so they cannot go
 * through the public LiipImagine cache. Thumbnailing is therefore done in-process via
 * ImageMagick (present in production); when it is unavailable (e.g. CI) this returns null
 * and the caller falls back to streaming the original bytes.
 */
class CandidatePreviewThumbnailer
{
    private const MAX_SIZE = 320;

    /**
     * @return string|null JPEG thumbnail bytes, or null if no thumbnail could be produced
     */
    public function thumbnail(string $bytes): ?string
    {
        if (!\extension_loaded('imagick')) {
            return null;
        }

        $imagick = new \Imagick();

        try {
            $imagick->readImageBlob($bytes);
            $imagick->setImageFormat('jpeg');
            $imagick->thumbnailImage(self::MAX_SIZE, self::MAX_SIZE, true);
            $imagick->setImageCompressionQuality(80);
            // A preview needs no metadata — strip it to keep the thumbnail small.
            $imagick->stripImage();

            return $imagick->getImageBlob();
        } catch (\Throwable $exception) {
            return null;
        } finally {
            $imagick->clear();
        }
    }
}
