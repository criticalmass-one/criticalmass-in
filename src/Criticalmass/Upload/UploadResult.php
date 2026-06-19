<?php declare(strict_types=1);

namespace App\Criticalmass\Upload;

/**
 * Outcome of staging a single uploaded file in the unified upload: whether it was
 * a track or a photo, and what happened to it (matched to a ride, parked/staged for
 * review, or recognised as a duplicate). Surfaced per file in the upload UI.
 */
final readonly class UploadResult
{
    public const KIND_TRACK = 'track';
    public const KIND_PHOTO = 'photo';

    public const STATUS_MATCHED = 'matched';
    public const STATUS_PARKED = 'parked';
    public const STATUS_STAGED = 'staged';
    public const STATUS_DUPLICATE = 'duplicate';

    public function __construct(
        public string $kind,
        public string $status,
        public string $message,
    ) {
    }
}
