<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoUploader;

use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface PhotoUploaderInterface
{
    public function setUser(User $user): PhotoUploaderInterface;
    public function setRide(Ride $ride): PhotoUploaderInterface;
    public function setTrack(?Track $track = null): PhotoUploaderInterface;
    public function addFile(string $filename): PhotoUploaderInterface;
    public function addUploadedFile(UploadedFile $uploadedFile): PhotoUploaderInterface;
    public function addDirectory(string $directoryName): PhotoUploaderInterface;
    public function getAddedPhotoList(): array;
}
