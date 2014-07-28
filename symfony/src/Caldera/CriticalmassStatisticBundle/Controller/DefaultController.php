<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassStatisticBundle:Default:index.html.twig', array('name' => $name));
    }

    public function generateAction($heatmapId)
    {
        $heatmap = $this->getDoctrine()->getRepository('CalderaCriticalmassStatisticBundle:Heatmap')->findOneById($heatmapId);

        foreach ($heatmap->getRides() as $ride)
        {
            echo $ride->getId()."<br />";
            $gpxc = new GPXConverter();
            $gpxc->loadContentFromString($ride->getOptimizedGpxContent());
            $gpxc->parseContent();

            $pathArray = $gpxc->getPathArray();

            for ($zoom = 0; $zoom < 16; ++$zoom)
            {
                $osmmdc = new OSMMapDimensionCalculator($pathArray, $zoom);

                for ($tileX = $osmmdc->getLeftTile(); $tileX <= $osmmdc->getRightTile(); ++$tileX)
                {
                    for ($tileY = $osmmdc->getTopTile(); $tileY >= $osmmdc->getBottomTile(); --$tileY)
                    {
                        $tile = new Tile();
                        $tile->generatePlaceByTileXTileYZoom($tileX, $tileY, $zoom);
                        $tile->dropPathArray($pathArray);

                        $tp = new PNGTilePrinter($tile, $heatmap);
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
