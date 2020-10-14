<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Criticalmass\Ical\RideIcalGenerator;
use App\Entity\Ride;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends AbstractController
{
    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    public function icalAction(Ride $ride, RideIcalGenerator $rideIcalGenerator): Response
    {
        $content = $rideIcalGenerator
            ->generateForRide($ride)
            ->getSerializedContent();

        $response = new Response($content);

        $filename = sprintf('%s.ics', $ride->getTitle());

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'text/calendar');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s";', $filename));
        $response->headers->set('Content-length', strlen($content));

        return $response;
    }
}
