<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Generator;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Geo\EntityInterface\TrackInterface;
use App\Criticalmass\Heatmap\HeatmapInterface;
use App\Criticalmass\Heatmap\Path\PositionListToPathListConverter;
use App\Criticalmass\Util\ClassUtil;
use App\Entity\Track;
use Symfony\Bridge\Doctrine\RegistryInterface;

class HeatmapGenerator
{
    /** @var HeatmapInterface $heatmap */
    protected $heatmap;

    /** @var TrackToPositionListConverter $trackToPositionListConverter */
    protected $trackToPositionListConverter;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry, TrackToPositionListConverter $trackToPositionListConverter)
    {
        $this->registry = $registry;
        $this->trackToPositionListConverter = $trackToPositionListConverter;
    }

    public function setHeatmap(HeatmapInterface $heatmap): HeatmapGenerator
    {
        $this->heatmap = $heatmap;

        return $this;
    }

    public function generate(): HeatmapGenerator
    {
        $trackList = $this->collectUnpaintedTracks();

        return $this;
    }

    protected function collectUnpaintedTracks(): array
    {
        $parentEntity = $this->heatmap->getUser() ?? $this->heatmap->getCity() ?? $this->heatmap->getRide();

        $className = ClassUtil::getShortname($parentEntity);

        $repositoryMethod = sprintf('findBy%s', $className);

        return $this->registry->getRepository(Track::class)->$repositoryMethod($parentEntity);
    }
}