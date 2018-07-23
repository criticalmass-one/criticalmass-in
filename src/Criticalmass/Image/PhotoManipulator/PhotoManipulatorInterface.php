<?php declare(strict_types=1);

namespace App\Criticalmas\Image\PhotoManipulator;

use App\Criticalmas\Image\PhotoManipulator\PhotoInterface\PhotoInterface;

interface PhotoManipulatorInterface
{
    public function open(PhotoInterface $photo): PhotoManipulatorInterface;
    public function save(): string;

    public function rotate(int $angle): PhotoManipulatorInterface;
    public function censor(array $areaDataList, int $displayWidth): PhotoManipulatorInterface;
}
