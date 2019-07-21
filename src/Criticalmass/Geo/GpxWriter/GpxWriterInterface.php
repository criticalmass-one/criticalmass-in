<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\GpxWriter;

use App\Criticalmass\Geo\PositionList\PositionListInterface;

interface GpxWriterInterface
{
    public function getGpxContent(): string;
    public function setPositionList(PositionListInterface $positionList): GpxWriterInterface;
    public function saveGpxContent(string $filename): void;
    public function addGpxAttribute(string $attributeName, string $attributeValue): GpxWriterInterface;
    public function addStandardGpxAttributes(): GpxWriterInterface;
    public function generateGpxContent(): GpxWriterInterface;
}
