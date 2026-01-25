<?php declare(strict_types=1);

namespace App\Criticalmass\UploadableDataHandler;

use InvalidArgumentException;
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
        $mappingList = $this->propertyMappingFactory->fromObject($entity);

        /** @var PropertyMapping $mapping */
        foreach ($mappingList as $mapping) {
            $filename = $this->getFilename($mapping, $entity);

            if (!$filename || !$this->filesystem->fileExists($filename)) {
                return $entity;
            }

            foreach ($this->propertyList as $property) {
                $calculateMethodName = sprintf('calculate%s', ucfirst($property));

                $value = $this->$calculateMethodName($filename);

                // unfortunately there is no way to access the entity mapping list,
                // so we will just try and catch
                try {
                    $mapping->writeProperty($entity, $property, $value);
                } catch (InvalidArgumentException $exception) {
                    $this->assignPropertyWithoutMapping($entity, $property, $mapping->getFileNamePropertyName(), $value);
                }
            }
        }

        return $entity;
    }

    public function getFilenameProperty(string $fqcn): array
    {
        $tmpObj = new $fqcn();

        $mappingList = $this->propertyMappingFactory->fromObject($tmpObj);

        $list = [];

        /** @var PropertyMapping $mapping */
        foreach ($mappingList as $mapping) {
            $list[] = $mapping->getFileNamePropertyName();
        }

        return $list;
    }

    protected function getFilenameGetMethod(PropertyMapping $propertyMapping): string
    {
        return sprintf('get%s', ucfirst($propertyMapping->getFileNamePropertyName()));
    }

    protected function getFilename(PropertyMapping $propertyMapping, UploadableEntity $entity): ?string
    {
        $getFilenameMethod = $this->getFilenameGetMethod($propertyMapping);

        return $entity->$getFilenameMethod();
    }

    protected function calculateSize(string $filename): int
    {
        return $this->filesystem->fileSize($filename);
    }

    protected function calculateMimeType(string $filename): string
    {
        return $this->filesystem->mimeType($filename);
    }

    private function assignPropertyWithoutMapping(UploadableEntity $entity, string $property, string $filenamePropertyName, $value): UploadableEntity
    {
        $propertyPrefix = $this->guessPropertyPrefix($filenamePropertyName);

        $setMethodName = sprintf('set%s%s', ucfirst($propertyPrefix), ucfirst($property));
        $entity->$setMethodName($value);

        return $entity;
    }

    protected function guessPropertyPrefix(string $filenamePropertyName): string
    {
        preg_match_all('/((?:^|[A-Z])[a-z]+)/', $filenamePropertyName,$matches);

        return $matches[0][0];
    }
}
