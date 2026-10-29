<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Criticalmass\Ical\RideIcalGeneratorInterface;
use App\Entity\City;
use App\Repository\RideRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CalendarController extends AbstractController
{
    #[Route('/{citySlug}/ical', name: 'caldera_criticalmass_city_ical', priority: 100)]
    public function icalAction(
        RideIcalGeneratorInterface $rideIcalGenerator,
        RideRepository $rideRepository,
        City $city
    ): Response {
        $rides = $rideRepository->findRidesForCity($city);

        $content = $rideIcalGenerator
            ->generateForRides($rides)
            ->getSerializedContent();

        $response = new Response($content);

        $filename = sprintf('%s.ics', $city->getCity());

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'text/calendar');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s";', $filename));
        $response->headers->set('Content-length', (string) strlen($content));

        return $response;
    }
}
