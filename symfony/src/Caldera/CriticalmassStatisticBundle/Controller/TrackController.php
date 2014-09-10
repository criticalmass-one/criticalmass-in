<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\TraceTilePrinter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\GpxConverter;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\OSMMapDimensionCalculator;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\Tile;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\PNGTilePrinter;
use Symfony\Component\HttpFoundation\Response;

class TrackController extends Controller
{
    public function indexAction()
    {
        return $this->render('CalderaCriticalmassStatisticBundle:Track:index.html.twig');
    }
}
