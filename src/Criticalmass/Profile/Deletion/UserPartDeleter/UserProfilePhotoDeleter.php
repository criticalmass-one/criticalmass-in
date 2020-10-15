<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Deletion\UserPartDeleter;

use App\Entity\User;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;

class UserProfilePhotoDeleter extends AbstractUserPartDeleter
{
    protected FilesystemInterface $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function delete(User $user): bool
    {
        $imageName = $user->getImageName();

        try {
            $this->filesystem->delete($imageName);
        } catch (FileNotFoundException $fileNotFoundException) {
            return false;
        }

        return true;
    }
}
