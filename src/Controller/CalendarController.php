<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\SeoPage\SeoPageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends AbstractController
{
    public function indexAction(Request $request, SeoPageInterface $seoPage): Response
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

        $seoPage->setDescription('Kalender-Übersicht über weltweite Critical-Mass-Touren.');

        return $this->render('Calendar/index.html.twig', [
            'dateTime' => $dateTime,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
        ]);
    }
}
