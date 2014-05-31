<?php

namespace Caldera\CriticalmassHeatmapBundle\Controller;

use Caldera\CriticalmassHeatmapBundle\Utility\Path;
use Caldera\CriticalmassHeatmapBundle\Utility\PNGTilePrinter;
use Caldera\CriticalmassHeatmapBundle\Utility\Tile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $tile = new Tile();
        $tile->dropPath(new Path());

        $tp = new PNGTilePrinter($tile);
        $tp->printTile();

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        return $response;
    }
}
