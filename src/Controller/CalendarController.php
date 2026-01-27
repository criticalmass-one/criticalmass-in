<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\SeoPage\SeoPageInterface;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CalendarController extends AbstractController
{
    #[Route('/calendar', name: 'caldera_criticalmass_calendar', priority: 280)]
    public function indexAction(Request $request, SeoPageInterface $seoPage): Response
    {
        $year = $request->query->getInt('year', (int) Carbon::now()->format('Y'));
        $month = $request->query->getInt('month', (int) Carbon::now()->format('m'));

        try {
            $dateTimeSpec = sprintf('%d-%d-01', $year, $month);
            $dateTime = CarbonImmutable::parse($dateTimeSpec);
        } catch (\Exception $exception) {
            $dateTime = CarbonImmutable::now();
        }

        $previousMonth = $dateTime->subMonth();
        $nextMonth = $dateTime->addMonth();

        $seoPage->setDescription('Kalender-Übersicht über weltweite Critical-Mass-Touren.');

        return $this->render('Calendar/index.html.twig', [
            'dateTime' => $dateTime,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
        ]);
    }
}
