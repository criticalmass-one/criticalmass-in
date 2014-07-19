<?php

namespace Caldera\CriticalmassCoreBundle\Controller;

use Caldera\CriticalmassCoreBundle\Entity\Ride;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function gpxexportAction($rideId, $userId)
    {
        $positionArray = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Position')->findBy(array('ride' => $rideId, 'user' => $userId), array('timestamp' => 'ASC'));

        $writer = new \XMLWriter();
        $writer->openURI('php://output');
        $writer->startDocument('1.0');

        $writer->setIndent(4);

        $writer->startElement('gpx');
        $writer->writeAttribute('creator', 'criticalmass.in');
        $writer->writeAttribute('version', '0.1');
        $writer->writeAttribute('xmlns', 'http://www.topografix.com/GPX/1/1');
        $writer->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $writer->writeAttribute('xsi:schemaLocation', 'http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd');

        $writer->startElement('metadata');
        $writer->startElement('time');

        $dateTime = $positionArray[0]->getCreationDateTime();
        $writer->text($dateTime->format('Y-m-d').'T'.$dateTime->format('H:i:s').'Z');

        $writer->endElement();
        $writer->endElement();

        $writer->startElement('trk');
        $writer->startElement('trkseg');

        foreach ($positionArray as $position)
        {
            $hour = $position->getCreationDateTime()->format('H');

            if ($hour >= 19 and $hour < 23 and $position->getAccuracy() <= 350)
            {
                $writer->startElement('trkpt');
                $writer->writeAttribute('lat', $position->getLatitude());
                $writer->writeAttribute('lon', $position->getLongitude());

                $writer->startElement('ele');
                $writer->text($position->getAltitude());
                $writer->endElement();

                $writer->startElement('time');

                $dateTime = $position->getCreationDateTime();
                $writer->text($dateTime->format('Y-m-d').'T'.$dateTime->format('H:i:s').'Z');

                $writer->endElement();
                $writer->endElement();
            }
        }
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $writer->endDocument();

        ob_start();
        $writer->flush();
        $gpx = ob_get_contents();
        ob_flush();

        $response = new Response();
        $response->headers->set('content-type', 'text/plain');
        $response->setContent($gpx);

        return new Response();
    }

    public function standardridesAction($year, $month)
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findBy(array(), array('city' => 'ASC'));

        echo '<ul>';

        foreach ($cities as $city)
        {
            echo '<li>';
            echo '<strong>'.$city->getTitle().'</strong>';

            if ($city->getIsStandardable())
            {
                $ride = new Ride();
                $ride->setCity($city);

                $firstMonthDay = new \DateTime($year.'-'.$month.'-01 00:00:00');

                $nextDateTime = $firstMonthDay;

                $dayInterval = new \DateInterval('P1D');

                while ($firstMonthDay->format('w') != $city->getStandardDayOfWeek())
                {
                    $nextDateTime->add($dayInterval);
                }

                if ($city->getStandardWeekOfMonth() > 0)
                {
                    $weekInterval = new \DateInterval('P7D');

                    for ($weekOfMonth = 1; $weekOfMonth < $city->getStandardWeekOfMonth(); ++$weekOfMonth)
                    {
                        $nextDateTime->add($weekInterval);
                    }
                }
                else
                {
                    $weekInterval = new \DateInterval('P7D');

                    while ($nextDateTime->format('m') == $month)
                    {
                        $nextDateTime->add($weekInterval);
                    }

                    $nextDateTime->sub($weekInterval);
                }

                if ($city->getStandardTime())
                {
                    $timeInterval = new \DateInterval('PT'.$city->getStandardTime()->format('H').'H'.$city->getStandardTime()->format('i').'M');
                    $nextDateTime->add($timeInterval);
                    $ride->setDateTime($nextDateTime);
                    $ride->setHasTime(true);
                }
                else
                {
                    $ride->setDateTime($nextDateTime);
                    $ride->setHasTime(false);
                }

                if ($city->getStandardLocation())
                {
                    $ride->setLocation($city->getStandardLocation());
                    $ride->setLatitude($city->getStandardLatitude());
                    $ride->setLongitude($city->getStandardLongitude());
                    $ride->setHasLocation(true);
                }
                else
                {
                    $ride->setHasLocation(false);
                }

                echo '<br />Lege folgende Tour an:';
                echo '<ul>';

                if ($ride->getHasTime())
                {
                    echo '<li>Datum und Uhrzeit: '.$ride->getDateTime()->format('Y-m-d H:i').'</li>';
                }
                else
                {
                    echo '<li>Datum: '.$ride->getDateTime()->format('Y-m-d').', Uhrzeit ist bislang unbekannt</li>';
                }

                if ($ride->getHasLocation())
                {
                    echo '<li>Treffpunkt: '.$ride->getLocation().' ('.$ride->getLatitude().'/'.$ride->getLongitude().')</li>';
                }
                else
                {
                    echo '<li>Treffpunkt ist bislang unbekannt</li>';
                }

                echo '</ul>';
            }
            else
            {
                echo '<br />Lege keine Tourdaten für diese Stadt an.';
            }

            echo '</li>';
        }

        echo '</ul>';

        return new Response();
    }
}
