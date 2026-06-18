<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\Normalizer;

interface PhotoNormalizerInterface
{
    /**
     * Normalise an uploaded image for staging: HEIC/HEIF are converted to JPEG
     * (so everything downstream stays in web-friendly formats), other supported
     * formats pass through unchanged.
     *
     * @throws \RuntimeException if the format is unsupported or cannot be read/converted
     */
    public function normalize(string $filePath, string $originalName): NormalizedImage;
}
