<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoManipulator;

use App\Criticalmass\Image\PhotoManipulator\PhotoInterface\ManipulateablePhotoInterface;
use App\Criticalmass\Image\PhotoManipulator\Storage\PhotoStorageInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractPhotoManipulator implements PhotoManipulatorInterface
{
    /** @var ManipulateablePhotoInterface $photo */
    protected $photo;

    /** @var ImageInterface $image */
    protected $image;

    /** @var ImagineInterface $imagine */
    protected $imagine;

    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var PhotoStorageInterface $photoStorage */
    protected $photoStorage;

    public function __construct(RegistryInterface $registry, PhotoStorageInterface $photoStorage)
    {
        $this->registry = $registry;
        $this->photoStorage = $photoStorage;
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
