<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Remover;

use App\Entity\Heatmap;
use League\Flysystem\FilesystemInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class HeatmapRemover implements HeatmapRemoverInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    public function __construct(RegistryInterface $registry, FilesystemInterface $filesystem)
    {
        $this->registry = $registry;
        $this->filesystem = $filesystem;
    }

    public function remove(Heatmap $heatmap): HeatmapRemoverInterface
    {
        $this->flush($heatmap);

        $manager = $this->registry->getManager();

        $manager->remove($heatmap);

        $manager->flush();

        return $this;
    }

    public function flush(Heatmap $heatmap): HeatmapRemoverInterface
    {
        $this->filesystem->deleteDir($heatmap->getIdentifier());

        $manager = $this->registry->getManager();

        foreach ($heatmap->getHeatmapTracks() as $heatmapTrack) {
            $manager->remove($heatmapTrack);
        }

        $manager->flush();

        return $this;
    }
}
