<?php declare(strict_types=1);

namespace App\Command\Upload;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

class UploadMappingResolver implements UploadMappingResolverInterface
{
    public function __construct(
        private readonly PropertyMappingFactory $propertyMappingFactory,
    ) {
    }

    public function resolve(string $fqcn): array
    {
        $tmpObject = new $fqcn();
        $mappings = $this->propertyMappingFactory->fromObject($tmpObject);

        $result = [];

        /** @var PropertyMapping $mapping */
        foreach ($mappings as $mapping) {
            $result[] = new UploadMappingInfo(
                $mapping->getFileNamePropertyName(),
                $mapping->getMappingName(),
            );
        }

        return $result;
    }
}
