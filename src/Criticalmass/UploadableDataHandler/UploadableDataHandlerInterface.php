<?php declare(strict_types=1);

namespace App\Criticalmass\UploadableDataHandler;

interface UploadableDataHandlerInterface
{
    public function setEntityClassname(string $entityClassname): UploadableDataHandlerInterface;
    public function calculateForEntity(UploadableEntity $entity): UploadableEntity;
}