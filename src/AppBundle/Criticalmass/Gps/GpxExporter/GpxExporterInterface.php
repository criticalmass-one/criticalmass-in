<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Gps\GpxExporter;

/** @deprecated */
interface GpxExporterInterface
{
    public function setPositionArray(array $positionArray): GpxExporterInterface;
    public function execute(): GpxExporterInterface;
    public function getGpxContent(): string;
}
