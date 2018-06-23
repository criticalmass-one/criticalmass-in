<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Gps\GpxExporter;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/** @deprecated */
abstract class AbstractGpxExporter implements GpxExporterInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var array $positionArray */
    protected $positionArray;

    /** @var string $gpxContent */
    protected $gpxContent = null;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function setPositionArray(array $positionArray): GpxExporterInterface
    {
        $this->positionArray = $positionArray;

        return $this;
    }

    public function execute(): GpxExporterInterface
    {
        if (count($this->positionArray) > 0) {
            $this->generateGpxContent();
        }

        return $this;
    }

    protected abstract function generateGpxContent(): GpxExporterInterface;

    public function getGpxContent(): string
    {
        return $this->gpxContent;
    }
} 
