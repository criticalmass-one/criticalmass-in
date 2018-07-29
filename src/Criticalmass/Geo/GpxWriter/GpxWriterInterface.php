<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\GpxWriter;

interface GpxWriterInterface
{
    public function getGpxContent(): string;

    public function saveGpxContent(string $filename): void;

    public function addGpxAttribute(string $attributeName, string $attributeValue): GpxWriterInterface;

    public function addStandardGpxAttributes(): GpxWriterInterface;

    public function generateGpxContent(): void;
}
