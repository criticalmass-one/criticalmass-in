<?php declare(strict_types=1);

namespace Criticalmass\Component\Gps\GpxExporter;

/** @deprecated */
interface GpxExporterInterface
{
    public function setPositionArray(array $positionArray): GpxExporterInterface;
    public function execute(): GpxExporterInterface;
    public function getGpxContent(): string;
}
