<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator;

use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class PhotoManipulator extends AbstractPhotoManipulator
{
    public function rotate(int $angle): PhotoManipulatorInterface
    {
        $this->image->rotate($angle);

        return $this;
    }

    public function censor(array $areaDataList, int $displayWidth): PhotoManipulatorInterface
    {
        $factor = $this->image->getSize()->getWidth() / $displayWidth;

        foreach ($areaDataList as $areaData) {
            $topLeftPoint = new Point($areaData->x * $factor, $areaData->y * $factor);
            $dimension = new Box($areaData->width * $factor, $areaData->height * $factor);

            $this->applyBlurArea($topLeftPoint, $dimension);
        }

        return $this;
    }

    protected function applyBlurArea(PointInterface $topLeftPoint, BoxInterface $dimension): void
    {
        $blurImage = $this->image->copy();

        $pixelateDimension = $dimension->scale(0.025);

        $blurImage
            ->crop($topLeftPoint, $dimension)
            ->resize($pixelateDimension, ImageInterface::FILTER_CUBIC)
            ->resize($dimension, ImageInterface::FILTER_CUBIC)
        ;

        $this->image->paste($blurImage, $topLeftPoint);
    }
}
