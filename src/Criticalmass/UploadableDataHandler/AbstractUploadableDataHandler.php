<?php declare(strict_types=1);

namespace App\Criticalmass\UploadableDataHandler;

use League\Flysystem\FilesystemInterface;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

abstract class AbstractUploadableDataHandler implements UploadableDataHandlerInterface
{
    /** @var PropertyMappingFactory $propertyMappingFactory */
    protected $propertyMappingFactory;

    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    public function __construct(PropertyMappingFactory $propertyMappingFactory, FilesystemInterface $filesystem)
    {
        $this->propertyMappingFactory = $propertyMappingFactory;
        $this->filesystem = $filesystem;
    }
}
