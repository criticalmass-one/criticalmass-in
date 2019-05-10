<?php declare(strict_types=1);

namespace App\Criticalmass\Image\GoogleCloud\ExportDataHandler;

use App\Criticalmass\UploadableDataHandler\UploadableDataHandler;

class ExportDataHandler extends UploadableDataHandler implements ExportDataHandlerInterface
{
    /** @var array $propertyList */
    protected $propertyList = [
        'size',
        'mimeType',
        'googleCloudHash',
    ];

    protected function calculateGoogleCloudHash(string $filename): string
    {
        return base64_encode(md5($this->filesystem->read($filename)));
    }
}