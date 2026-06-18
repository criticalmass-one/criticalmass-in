<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\Normalizer;

/**
 * Result of normalising an uploaded image: the bytes to stage, the resulting
 * MIME type and the file extension to store under (HEIC/HEIF become JPEG).
 */
final readonly class NormalizedImage
{
    public function __construct(
        public string $bytes,
        public string $mimeType,
        public string $extension,
    ) {
    }
}
