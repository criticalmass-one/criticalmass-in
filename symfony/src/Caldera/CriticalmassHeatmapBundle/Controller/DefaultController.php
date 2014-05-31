<?php

namespace Caldera\CriticalmassHeatmapBundle\Controller;

use Caldera\CriticalmassHeatmapBundle\Utility\GPXConverter;
use Caldera\CriticalmassHeatmapBundle\Utility\OSMMapDimensionCalculator;
use Caldera\CriticalmassHeatmapBundle\Utility\Path;
use Caldera\CriticalmassHeatmapBundle\Utility\PNGTilePrinter;
use Caldera\CriticalmassHeatmapBundle\Utility\Position;
use Caldera\CriticalmassHeatmapBundle\Utility\OSMCoordCalculator;
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

        $zoom = 13;

        $pathArray = $gpxc->getPathArray();

        $osmmdc = new OSMMapDimensionCalculator($pathArray, $zoom);

        echo $osmmdc->getLeftTile()." ".$osmmdc->getRightTile();
        for ($tileX = $osmmdc->getLeftTile(); $tileX <= $osmmdc->getRightTile(); ++$tileX)
        {
            for ($tileY = $osmmdc->getTopTile(); $tileY >= $osmmdc->getBottomTile(); --$tileY)
            {
                $tile = new Tile();
                //$tile->generatePlaceByLatitudeLongitudeZoom(53.6632620, 9.7335000, $zoom);
                $tile->generatePlaceByTileXTileYZoom($tileX, $tileY, $zoom);
                $tile->dropPathArray($pathArray);

                $tp = new PNGTilePrinter($tile);
                $tp->printTile();
                $tp->saveTile();
            }
        }


        $response = new Response();
        //$response->setContent($tp->getImageFileContent());
        //$response->headers->set('Content-Type', 'image/png');
        return $response;
    }
}
