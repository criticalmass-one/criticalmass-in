<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Entity\Ride;
use Sabre\VObject\Component\VCalendar;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends AbstractController
{
    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    public function icalAction(Ride $ride): Response
    {
        $timezone = new \DateTimeZone($ride->getCity()->getTimezone());
        $durationInterval = new \DateInterval('PT2H');

        $startDateTime = clone $ride->getDateTime();
        $startDateTime->setTimezone($timezone);

        $endDateTime = clone $ride->getDateTime();
        $endDateTime->setTimezone($timezone)->add($durationInterval);

        $vcalendar = new VCalendar([
            'VEVENT' => [
                'SUMMARY' => $ride->getTitle(),
                'DTSTART' => $startDateTime,
                'DTEND'   => $endDateTime,
            ],
        ]);

        $filename = sprintf('%s.ics', $ride->getTitle());
        
        $content = $vcalendar->serialize();

        $response = new Response($content);

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'text/calendar');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '";');
        $response->headers->set('Content-length', strlen($content));

        return $response;
    }
}
