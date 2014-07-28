<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\TraceTilePrinter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\GpxConverter;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\OSMMapDimensionCalculator;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\Tile;
use Caldera\CriticalmassStatisticBundle\Utility\Heatmap\PNGTilePrinter;
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

            for ($zoom = 1; $zoom < 17; ++$zoom)
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
                        $tp = new TraceTilePrinter($tile, $heatmap, $track->getTicket());
                        $tp->printTile();
                        $tp->saveTile();

                        echo "Done: ".$tile->getOsmXTile().",".$tile->getOsmYTile().",".$tile->getOsmZoom().",".$track->getUsername();
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
