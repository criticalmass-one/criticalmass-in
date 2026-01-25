<?php declare(strict_types=1);

namespace App\Criticalmass\UploadableDataHandler;

use League\Flysystem\FilesystemOperator;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

abstract class AbstractUploadableDataHandler implements UploadableDataHandlerInterface
{
    public function __construct(
        protected PropertyMappingFactory $propertyMappingFactory,
        protected FilesystemOperator $filesystem
    ) {
    }
}
