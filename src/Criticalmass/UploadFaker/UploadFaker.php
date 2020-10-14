<?php declare(strict_types=1);

namespace App\Criticalmass\UploadFaker;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFaker extends AbstractUploadFaker
{
    public function fakeUpload(FakeUploadable $uploadable, string $propertyName, string $fileContent, string $originalFilename = null): string
    {
        $tmpFilename = $this->generateFilename();

        $this->dumpContentToTmp($tmpFilename, $fileContent);

        $file = $this->createUploadedFile($tmpFilename, $originalFilename);

        $setMethodName = $this->generateSetMethodName($propertyName);

        $uploadable->$setMethodName($file);

        return $tmpFilename;
    }

    protected function generateSetMethodName(string $propertyName): string
    {
        return sprintf('set%s', ucfirst($propertyName));
    }

    protected function createUploadedFile(string $filename, string $originalFilename): UploadedFile
    {
        return new UploadedFile($filename, $originalFilename, null, null, true);
    }

    protected function generateFilename(): string
    {
        return sprintf('%s/%s', self::TMP, uniqid('', true));
    }

    protected function dumpContentToTmp(string $filename, string $fileContent): UploadFaker
    {
        $this->filesystem->dumpFile($filename, $fileContent);

        return $this;
    }

    protected function deleteTmpFile(string $filename): UploadFaker
    {
        $this->filesystem->remove($filename);

        return $this;
    }
}