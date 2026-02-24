<?php declare(strict_types=1);

namespace App\Command\Upload;

class UploadMappingInfo
{
    public function __construct(
        public readonly string $filenameProperty,
        public readonly string $mappingName,
    ) {
    }
}
