<?php

namespace AppBundle\Controller;

use AppBundle\Criticalmass\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends AbstractController
{
    public function indexAction(Request $request, SeoPage $seoPage): Response
    {
        $year = $request->query->getInt('year', (new \DateTime())->format('Y'));
        $month = $request->query->getInt('month', (new \DateTime())->format('m'));

        try {
            $dateTimeSpec = sprintf('%d-%d-01', $year, $month);
            $dateTime = new \DateTimeImmutable($dateTimeSpec);
        } catch (\Exception $exception) {
            $dateTime = new \DateTimeImmutable();
        }

        $monthInterval = new \DateInterval('P1M');
        $previousMonth = $dateTime->sub($monthInterval);
        $nextMonth = $dateTime->add($monthInterval);

        $seoPage->setDescription('Kalender-Übersicht über weltweitere Critical-Mass-Touren.');

        return $this->render(
            'AppBundle:Calendar:index.html.twig', [
                'dateTime' => $dateTime,
                'previousMonth' => $previousMonth,
                'nextMonth' => $nextMonth,
            ]
        );
    }
}
