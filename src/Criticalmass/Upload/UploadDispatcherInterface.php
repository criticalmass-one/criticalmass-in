<?php declare(strict_types=1);

namespace App\Criticalmass\Upload;

use App\Entity\User;

interface UploadDispatcherInterface
{
    /**
     * Routes a single uploaded file to the matching handler (track vs. photo) by its
     * extension and returns what happened to it.
     *
     * @throws \RuntimeException if the file type is unsupported or cannot be processed
     */
    public function dispatch(string $filePath, string $originalName, User $user): UploadResult;
}
