<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator;

use App\Criticalmass\Image\PhotoManipulator\PhotoInterface\ManipulateablePhotoInterface;

interface PhotoManipulatorInterface
{
    public function open(ManipulateablePhotoInterface $photo): PhotoManipulatorInterface;
    public function save(): string;

    public function rotate(int $angle): PhotoManipulatorInterface;
    public function censor(array $areaDataList, int $displayWidth): PhotoManipulatorInterface;
}
