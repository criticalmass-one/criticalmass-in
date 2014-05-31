<?php

namespace Caldera\CriticalmassHeatmapBundle\Controller;

use Caldera\CriticalmassHeatmapBundle\Utility\Path;
use Caldera\CriticalmassHeatmapBundle\Utility\PNGTilePrinter;
use Caldera\CriticalmassHeatmapBundle\Utility\Position;
use Caldera\CriticalmassHeatmapBundle\Utility\Tile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $path = new Path(new Position(53.1, 8.5), new Position(52.9, 9.5));

        $tile = new Tile();
        $tile->generatePlaceByLatitudeLongitudeZoom(53, 9, 8);
        $tile->dropPath($path);

        $tp = new PNGTilePrinter($tile);
        $tp->printTile();

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        return $response;
    }
}
