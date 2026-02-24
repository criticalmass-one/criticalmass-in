<?php declare(strict_types=1);

namespace App\Command\Upload;

interface UploadMappingResolverInterface
{
    /** @return list<UploadMappingInfo> */
    public function resolve(string $fqcn): array;
}
