<?php declare(strict_types=1);

namespace App\Criticalmass\UploadableDataHandler;

use League\Flysystem\FilesystemOperator;
use Vich\UploaderBundle\Mapping\PropertyMappingFactoryInterface;

abstract class AbstractUploadableDataHandler implements UploadableDataHandlerInterface
{
    public function __construct(
        protected PropertyMappingFactoryInterface $propertyMappingFactory,
        protected FilesystemOperator $filesystem
    ) {
    }
}
