<?php declare(strict_types=1);

namespace App\Controller\Heatmap;

use App\Controller\AbstractController;
use App\Criticalmass\Heatmap\Generator\HeatmapGenerator;
use App\Entity\Heatmap;
use App\Repository\TrackRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;

class HeatmapController extends AbstractController
{
    public function fooAction(RegistryInterface $registry, HeatmapGenerator $heatmapGenerator): Response
    {
        $heatmap = $registry->getRepository(Heatmap::class)->find(1);

        $heatmapGenerator->setHeatmap($heatmap)->generate();

        return new Response('foo');
    }
}
