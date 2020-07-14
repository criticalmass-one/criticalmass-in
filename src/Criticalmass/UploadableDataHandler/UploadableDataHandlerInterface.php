<?php declare(strict_types=1);

namespace App\Criticalmass\UploadableDataHandler;

interface UploadableDataHandlerInterface
{
    public function calculateForEntity(UploadableEntity $entity): UploadableEntity;
    public function getFilenameProperty(string $fqcn): array;
}