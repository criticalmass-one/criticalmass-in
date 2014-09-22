<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\TraceTilePrinter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\GpxConverter;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\OSMMapDimensionCalculator;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\Tile;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassStatisticBundle:Default:index.html.twig', array('name' => $name));
    }

    public function generateAction($heatmapId)
    {
        $heatmap = $this->getDoctrine()->getRepository('CalderaCriticalmassStatisticBundle:Heatmap')->findOneById($heatmapId);

        foreach ($heatmap->getTracks() as $track)
        {
            $gpxc = new GpxConverter();
            $gpxc->loadContentFromString(stream_get_contents($track->getGpx()));
            $gpxc->parseContent();

            $pathArray = $gpxc->getPathArray();

            for ($zoom = 16; $zoom <= 18; ++$zoom)
            {
                $osmmdc = new OSMMapDimensionCalculator($pathArray, $zoom);

                for ($tileX = $osmmdc->getLeftTile(); $tileX <= $osmmdc->getRightTile(); ++$tileX)
                {
                    for ($tileY = $osmmdc->getTopTile(); $tileY >= $osmmdc->getBottomTile(); --$tileY)
                    {
                        $tile = new Tile();
                        $tile->generatePlaceByTileXTileYZoom($tileX, $tileY, $zoom);
                        $tile->dropPathArray($pathArray);

                        //$tp = new PNGTilePrinter($tile, $heatmap);
                        $tp = new TraceTilePrinter($tile, $heatmap, $track);
                        $tp->printTile();
                        $tp->saveTile();
                    }
                }
            }
        }

        $response = new Response();
        //$response->setContent($tp->getImageFileContent());
        //$response->headers->set('Content-Type', 'image/png');
        return $response;
    }
}
