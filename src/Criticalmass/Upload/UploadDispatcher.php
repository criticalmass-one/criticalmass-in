<?php declare(strict_types=1);

namespace App\Criticalmass\Upload;

use App\Criticalmass\Upload\Handler\PhotoUploadHandler;
use App\Criticalmass\Upload\Handler\TrackUploadHandler;
use App\Entity\User;

/**
 * Routes an uploaded file to the right staging handler based on its extension. This is
 * the single entry point behind the unified upload endpoint: GPX/FIT go to the track
 * pipeline, image files to the photo pipeline, everything else is rejected.
 */
class UploadDispatcher implements UploadDispatcherInterface
{
    private const TRACK_EXTENSIONS = ['gpx', 'fit'];
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'heic', 'heif'];

    public function __construct(
        private readonly TrackUploadHandler $trackUploadHandler,
        private readonly PhotoUploadHandler $photoUploadHandler,
    ) {
    }

    public function dispatch(string $filePath, string $originalName, User $user): UploadResult
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (in_array($extension, self::TRACK_EXTENSIONS, true)) {
            return $this->trackUploadHandler->handle($filePath, $originalName, $user);
        }

        if (in_array($extension, self::IMAGE_EXTENSIONS, true)) {
            return $this->photoUploadHandler->handle($filePath, $originalName, $user);
        }

        throw new \RuntimeException(sprintf(
            'Das Dateiformat „.%s“ wird nicht unterstützt — erlaubt sind GPX-/FIT-Tracks und Bilddateien (JPG, PNG, WebP, GIF, HEIC).',
            $extension,
        ));
    }
}
