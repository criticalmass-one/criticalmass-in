<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TimelineController extends AbstractController
{
    #[Route(
        '/timeline/{year}/{month}',
        name: 'caldera_criticalmass_timeline_yearmonth',
        requirements: ['year' => '([0-9]{4,4})', 'month' => '([0-9]{2,2})'],
        priority: 130
    )]
    public function yearmonthAction(int $year, int $month): Response
    {
        $lowerLimitDateTime = new \DateTime('2010-01-01');

        $startDateTime = new \DateTime($year . '-' . $month . '-01');

        if ($startDateTime < $lowerLimitDateTime) {
            $startDateTime = new \DateTime();
        }

        return $this->render('Timeline/yearmonth.html.twig', [
            'startDateTime' => $startDateTime,
            'apiUrl' => '/api/timeline/{year}/{month}',
            'year' => (int) $startDateTime->format('Y'),
            'month' => (int) $startDateTime->format('m'),
        ]);
    }

    #[Route(
        '/timeline',
        name: 'caldera_criticalmass_timeline_index',
        priority: 130
    )]
    public function indexAction(): RedirectResponse
    {
        $dateTime = new \DateTime();

        return $this->redirectToRoute('caldera_criticalmass_timeline_yearmonth', [
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
        ]);
    }
}
