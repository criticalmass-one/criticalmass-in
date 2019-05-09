<?php declare(strict_types=1);

namespace App\Criticalmass\UploadableDataHandler;

use Vich\UploaderBundle\Mapping\PropertyMapping;

class UploadableDataHandler extends AbstractUploadableDataHandler
{
    /** @var array $propertyList */
    protected $propertyList = [
        'size',
        'mimeType',
    ];

    public function calculateForEntity(UploadableEntity $entity): UploadableEntity
    {
        $mapping = $this->getMapping($entity);

        $filename = $this->getFilename($mapping, $entity);

        if (!$this->filesystem->has($filename)) {
            return $entity;
        }

        foreach ($this->propertyList as $property) {
            $calculateMethodName = sprintf('calculate%s', ucfirst($property));

            $value = $this->$calculateMethodName($filename);

            $mapping->writeProperty($entity, $property, $value);
        }

        return $entity;
    }

    public function getFilenameProperty(string $fqcn): string
    {
        $tmpObj = new $fqcn();

        return $this->getMapping($tmpObj)->getFileNamePropertyName();
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

    protected function calculateSize(string $filename)
    {
        return $this->filesystem->getSize($filename);
    }

    protected function calculateMimeType(string $filename)
    {
        return $this->filesystem->getSize($filename);
    }
}
