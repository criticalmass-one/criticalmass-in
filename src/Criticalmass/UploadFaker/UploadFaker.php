<?php declare(strict_types=1);

namespace App\Criticalmass\UploadFaker;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFaker extends AbstractUploadFaker
{
    public function fakeUpload(FakeUploadable $uploadable, string $propertyName, string $fileContent): FakeUploadable
    {
        $filename = $this->generateFilename();

        $this->dumpContentToTmp($filename, $fileContent);

        $file = $this->createUploadedFile($filename);

        $setMethodName = $this->generateSetMethodName($propertyName);

        $uploadable->$setMethodName($file);

        return $uploadable;
    }

    protected function generateSetMethodName(string $propertyName): string
    {
        return sprintf('set%s', ucfirst($propertyName));
    }

    protected function createUploadedFile(string $filename): UploadedFile
    {
        return new UploadedFile($filename, $filename, null, null, true);
    }

    protected function generateFilename(): string
    {
        return sprintf('%s/%s', self::TMP, uniqid('', true));
    }

    protected function dumpContentToTmp(string $filename, string $fileContent): UploadFaker
    {
        dump($filename);
        $this->filesystem->dumpFile($filename, $fileContent);

        return $this;
    }

    protected function deleteTmpFile(string $filename): UploadFaker
    {
        $this->filesystem->remove($filename);

        return $this;
    }
}