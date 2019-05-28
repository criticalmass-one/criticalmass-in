<?php declare(strict_types=1);

namespace App\EntityInterface;

use App\Criticalmass\UploadableDataHandler\UploadableEntity;
use Symfony\Component\HttpFoundation\File\File;

interface PhotoInterface extends UploadableEntity
{
    public function getImageFile(): ?File;
    public function setImageFile(File $imageFile = null): PhotoInterface;
    public function getImageName(): ?string;
    public function setImageName(string $imageName = null): PhotoInterface;
    public function getImageSize(): ?int;
    public function setImageSize(int $imageSize = null): PhotoInterface;
    public function getImageMimeType(): ?string;
    public function setImageMimeType(string $imageMimeType = null): PhotoInterface;
}
