<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator\PhotoInterface;

use App\EntityInterface\PhotoInterface;

interface ManipulateablePhotoInterface extends PhotoInterface
{
    public function getBackupName(): ?string;
    public function setBackupName(string $backupName = null): ManipulateablePhotoInterface;
}
