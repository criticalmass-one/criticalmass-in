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
        //$gpxc->loadContentFromFile("/Applications/XAMPP/htdocs/2011-06-24.gpx");
        //$gpxc->loadContentFromFile("/Applications/XAMPP/htdocs/2011-08-26.gpx");
        //$gpxc->loadContentFromFile("/Applications/XAMPP/htdocs/2011-09-30.gpx");
        //$gpxc->loadContentFromFile("/Applications/XAMPP/htdocs/2011-10-28.gpx");
        //$gpxc->loadContentFromFile("/Applications/XAMPP/htdocs/2011-11-25.gpx");
        $gpxc->loadContentFromFile("/Applications/XAMPP/htdocs/2011-12-30.gpx");
        $gpxc->parseContent();

        $pathArray = $gpxc->getPathArray();

        for ($zoom = 10; $zoom < 16; ++$zoom)
        {
            $osmmdc = new OSMMapDimensionCalculator($pathArray, $zoom);

            for ($tileX = $osmmdc->getLeftTile(); $tileX <= $osmmdc->getRightTile(); ++$tileX)
            {
                for ($tileY = $osmmdc->getTopTile(); $tileY >= $osmmdc->getBottomTile(); --$tileY)
                {
                    $tile = new Tile();
                    $tile->generatePlaceByTileXTileYZoom($tileX, $tileY, $zoom);
                    $tile->dropPathArray($pathArray);

                    $tp = new PNGTilePrinter($tile);
                    $tp->printTile();
                    $tp->saveTile();
                }
            }
        }

        $response = new Response();
        //$response->setContent($tp->getImageFileContent());
        //$response->headers->set('Content-Type', 'image/png');
        return $response;
    }
}
