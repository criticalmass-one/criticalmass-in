<?php declare(strict_types=1);

namespace App\Criticalmass\UploadableDataHandler;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

class UploadableDataHandler implements UploadableDataHandlerInterface
{
    /** @var PropertyMappingFactory $propertyMappingFactory */
    protected $propertyMappingFactory;

    protected $propertyCallList = [
        'size' => 'filesize',
        'mimeType' => 'mime_content_type',
    ];

    public function __construct(PropertyMappingFactory $propertyMappingFactory)
    {
        $this->propertyMappingFactory = $propertyMappingFactory;
    }

    public function calculateForEntity(UploadableEntity $entity): UploadableEntity
    {
        /** @var PropertyMapping $mapping */
        $mapping = $this->propertyMappingFactory->fromObject($entity)[0];

        $getFilenameMethod = sprintf('get%s', ucfirst($mapping->getFileNamePropertyName()));

        $filename = sprintf('%s/%s', $mapping->getUploadDestination(), $entity->$getFilenameMethod());

        if (!file_exists($filename)) {
            return $entity;
        }

        foreach ($this->propertyCallList as $property => $functionName) {
            $value = $functionName($filename);

            $mapping->writeProperty($entity, $property, $value);
        }

        return $entity;
    }
}