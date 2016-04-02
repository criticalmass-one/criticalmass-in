<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassModelBundle\Entity\RideEstimate;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideEstimateType;
use Symfony\Component\HttpFoundation\Request;

class StatisticController extends AbstractController
{
    public function citystatisticAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $rides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Statistic:citystatistic.html.twig',
            [
                'city' => $city,
                'rides' => $rides
            ]
        );
    }

    /**
     * Prepares a template with a pie chart containing the estimated participants of every tour in a specific month.
     *
     * @param Request $request
     * @param $year
     * @param $month
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rideparticipantsAction(Request $request, $year, $month)
    {
        if (!$year or !$month)
        {
            $dateTime = new \DateTime();
        }
        else
        {
            $dateTime = new \DateTime($year.'-'.$month.'-01');
        }

        /* Okay, now take the rides from the database and push them into our new array $rides with their estimated par-
        ticipants as their key so we can sort them afterwards. We’ll use a two-dimensional array here to cover cities
        with identical estimates. */
        $rides = array();
        $rideResult = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findRidesByDateTimeMonth($dateTime);

        foreach ($rideResult as $ride)
        {
            $rides[$ride->getEstimatedParticipants()][] = $ride;
        }

        /* Now sort this shit and keep the old keys as the index. */
        krsort($rides);

        /* We’ll use this interval to calculate the previous and the next month for the navigation. */
        $monthInterval = new \DateInterval('P1M');

        $previousMonth = clone $dateTime;
        $previousMonth->sub($monthInterval);

        $nextMonth = clone $dateTime;
        $nextMonth = $nextMonth->add($monthInterval);

        /* If the next month would be in the future, skip this case. */
        if ($nextMonth > new \DateTime())
        {
            $nextMonth = null;
        }

        return $this->render('CalderaCriticalmassStatisticBundle:Statistic:rideparticipants.html.twig', array('rides' => $rides, 'currentMonth' => $dateTime, 'previousMonth' => $previousMonth, 'nextMonth' => $nextMonth));
    }

    public function estimateformAction(Request $request, $rideId)
    {
        $ride = $this->getRideRepository()->find($rideId);

        $estimate = new RideEstimate();
        $form = $this->createForm(new RideEstimateType(), $estimate, array('action' => $this->generateUrl('caldera_criticalmass_statistic_ride_estimate', array('citySlug' => $ride->getCity()->getMainSlugString(), 'rideDate' => $ride->getDateTime()->format('Y-m-d')))));

        return $this->render('CalderaCriticalmassSiteBundle:Statistic:estimateform.html.twig', array('form' => $form->createView()));
    }

    public function estimateAction(Request $request, $citySlug, $rideDate)
    {
        $citySlugObj = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

        if (!$citySlugObj)
        {
            throw new NotFoundHttpException('Wir haben leider keine Stadt in der Datenbank, die sich mit '.$citySlug.' identifiziert.');
        }

        $city = $citySlugObj->getCity();

        try {
            $rideDateTime = new \DateTime($rideDate);
        }
        catch (\Exception $e)
        {
            throw new NotFoundHttpException('Mit diesem Datum können wir leider nichts anfange. Bitte gib ein Datum im Format YYYY-MM-DD an.');
        }

        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findCityRideByDate($city, $rideDateTime);

        if (!$ride)
        {
            throw new NotFoundHttpException('Wir haben leider keine Tour in '.$city->getCity().' am '.$rideDateTime->format('d. m. Y').' gefunden.');
        }

        $estimate = new RideEstimate();
        $form = $this->createForm(new RideEstimateType(), $estimate, array('action' => $this->generateUrl('caldera_criticalmass_statistic_ride_estimate', array('citySlug' => $ride->getCity()->getMainSlugString(), 'rideDate' => $ride->getDateTime()->format('Y-m-d')))));

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $estimate->setRide($ride);
            $estimate->setUser($this->getUser());

            // TODO: This is simply bullshit. Really, we should try to rely on locales here. The validator of our entity accepts also dots or commas
            // but before pushing that shit into our database we need to replace all commas with a dot

            if ($estimate->getEstimatedDistance())
            {
                $estimate->setEstimatedDistance(str_replace(',', '.', $estimate->getEstimatedDistance()));
            }

            if ($estimate->getEstimatedDuration())
            {
                $estimate->setEstimatedDuration(str_replace(',', '.', $estimate->getEstimatedDuration()));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($estimate);
            $em->flush();

            $this->get('caldera.criticalmassstatistic.rideestimate')->calculateEstimates($ride);
        }
        else
        {
            return $this->render('CalderaCriticalmassStatisticBundle:Ride:estimatefailure.html.twig', array('form' => $form->createView(), 'ride' => $ride));
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_ride_show', array('citySlug' => $citySlug, 'rideDate' => $ride->getDateTime()->format('Y-m-d'))));
    }

    public function generateAction($heatmapId)
    {
        $heatmap = $this->getDoctrine()->getRepository('CalderaCriticalmassStatisticBundle:Heatmap')->findOneById($heatmapId);

        foreach ($heatmap->getTracks() as $track)
        {
            $gpxc = new GpxConverter();
            $gpxc->loadContentFromString($track->getGpx());
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
