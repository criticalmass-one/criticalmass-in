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
    public function yearmonthAction(int $year, int $month): RedirectResponse
    {
        return $this->redirectToRoute('caldera_criticalmass_timeline_index');
    }

    #[Route(
        '/timeline',
        name: 'caldera_criticalmass_timeline_index',
        priority: 130
    )]
    public function indexAction(): Response
    {
        return $this->render('Timeline/yearmonth.html.twig', [
            'apiUrl' => '/api/timeline',
        ]);
    }
}
