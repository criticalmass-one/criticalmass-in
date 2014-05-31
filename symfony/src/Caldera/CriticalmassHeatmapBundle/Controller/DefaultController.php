<?php

namespace Caldera\CriticalmassHeatmapBundle\Controller;

use Caldera\CriticalmassHeatmapBundle\Utility\GPXConverter;
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
        $gpxc = new GPXConverter();
        $gpxc->loadContentFromFile("/Applications/XAMPP/htdocs/evening.gpx");
        $gpxc->parseContent();
        $pathArray = $gpxc->getPathArray();

        $tile = new Tile();
        $tile->generatePlaceByLatitudeLongitudeZoom(53.6632620, 9.7335000, 5);

        $tile->dropPathArray($pathArray);
        $tile->sortPixelList();

        $tp = new PNGTilePrinter($tile);
        $tp->printTile();

        $response = new Response();
        $response->setContent($tp->getImageFileContent());
        $response->headers->set('Content-Type', 'image/png');
        return $response;
    }
}
