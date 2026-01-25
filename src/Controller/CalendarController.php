<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\SeoPage\SeoPageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CalendarController extends AbstractController
{
    #[Route('/calendar', name: 'caldera_criticalmass_calendar', priority: 280)]
    public function indexAction(Request $request, SeoPageInterface $seoPage): Response
    {
        $year = $request->query->getInt('year', (int) (new \DateTime())->format('Y'));
        $month = $request->query->getInt('month', (int) (new \DateTime())->format('m'));

        try {
            $dateTimeSpec = sprintf('%d-%d-01', $year, $month);
            $dateTime = new \DateTimeImmutable($dateTimeSpec);
        } catch (\Exception $exception) {
            $dateTime = new \DateTimeImmutable();
        }

        $monthInterval = new \DateInterval('P1M');
        $previousMonth = $dateTime->sub($monthInterval);
        $nextMonth = $dateTime->add($monthInterval);

        $seoPage->setDescription('Kalender-Übersicht über weltweite Critical-Mass-Touren.');

        return $this->render('Calendar/index.html.twig', [
            'dateTime' => $dateTime,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
        ]);
    }
}
