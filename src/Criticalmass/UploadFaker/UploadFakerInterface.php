<?php declare(strict_types=1);

namespace App\Criticalmass\UploadFaker;

interface UploadFakerInterface
{
    public function fakeUpload(FakeUploadable $uploadable, string $propertyName, string $fileContent, ?string $originalFilename = null): string;
}