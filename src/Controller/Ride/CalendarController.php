<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Criticalmass\Ical\RideIcalGeneratorInterface;
use App\Entity\Ride;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends AbstractController
{
    public function icalAction(
        RideIcalGeneratorInterface $rideIcalGenerator,
        Ride $ride
    ): Response {
        $content = $rideIcalGenerator
            ->generateForRide($ride)
            ->getSerializedContent();

        $response = new Response($content);

        $filename = sprintf('%s.ics', $ride->getTitle());

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'text/calendar');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s";', $filename));
        $response->headers->set('Content-length', (string) strlen($content));

        return $response;
    }
}
