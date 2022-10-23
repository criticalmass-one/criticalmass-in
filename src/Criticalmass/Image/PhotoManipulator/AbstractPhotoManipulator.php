<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator;

use App\Criticalmass\Image\PhotoManipulator\PhotoInterface\ManipulateablePhotoInterface;
use App\Criticalmass\Image\PhotoManipulator\Storage\PhotoStorageInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractPhotoManipulator implements PhotoManipulatorInterface
{
    /** @var ManipulateablePhotoInterface $photo */
    protected $photo;

    /** @var ImageInterface $image */
    protected $image;

    /** @var ImagineInterface $imagine */
    protected $imagine;

    public function __construct(protected ManagerRegistry $registry, protected PhotoStorageInterface $photoStorage)
    {
    }

    public function open(ManipulateablePhotoInterface $photo): PhotoManipulatorInterface
    {
        $this->photo = $photo;

        $this->image = $this->photoStorage->open($photo);

        return $this;
    }

    public function save(): string
    {
        return $this->photoStorage->save($this->photo, $this->image);
    }

    public function getPhoto(): ManipulateablePhotoInterface
    {
        return $this->photo;
    }
}
