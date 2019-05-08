<?php declare(strict_types=1);

namespace App\Criticalmass\UploadableDataHandler;

use Vich\UploaderBundle\Mapping\PropertyMapping;

class UploadableDataHandler extends AbstractUploadableDataHandler
{
    public function calculateForEntity(UploadableEntity $entity): UploadableEntity
    {
        $mapping = $this->getMapping($entity);

        $filename = $this->getFilename($mapping, $entity);

        if (!file_exists($filename)) {
            return $entity;
        }

        foreach ($this->propertyCallList as $property => $functionName) {
            $value = $functionName($filename);

            $mapping->writeProperty($entity, $property, $value);
        }

        return $entity;
    }

    protected function getMapping(UploadableEntity $entity): PropertyMapping
    {
        return $this->propertyMappingFactory->fromObject($entity)[0];
    }

    protected function getFilenameGetMethod(PropertyMapping $propertyMapping): string
    {
        return sprintf('get%s', ucfirst($propertyMapping->getFileNamePropertyName()));
    }

    protected function getFilename(PropertyMapping $propertyMapping, UploadableEntity $entity): string
    {
        $getFilenameMethod = $this->getFilenameGetMethod($propertyMapping);

        return sprintf('%s/%s', $propertyMapping->getUploadDestination(), $entity->$getFilenameMethod());
    }
}
