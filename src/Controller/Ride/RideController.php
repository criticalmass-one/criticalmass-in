<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Entity\Ride;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Event\View\ViewEvent;
use App\Form\Type\RideDisableType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Controller\AbstractController;
use App\Entity\Weather;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RideController extends AbstractController
{
    public function listAction(): Response
    {
        $ridesResult = $this->getRideRepository()->findRidesInInterval();

        $rides = [];

        /** @var Ride $ride */
        foreach ($ridesResult as $ride) {
            $rides[$ride->getDateTime()->format('Y-m-d')][] = $ride;
        }

        return $this->render('Ride/list.html.twig', [
            'rides' => $rides,
        ]);
    }

    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    public function showAction(Request $request, SeoPageInterface $seoPage, EventDispatcherInterface $eventDispatcher, Ride $ride): Response
    {
        $eventDispatcher->dispatch(ViewEvent::NAME, new ViewEvent($ride));

        $seoPage
            ->setDescription('Informationen, Strecken und Fotos von der Critical Mass in ' . $ride->getCity()->getCity() . ' am ' . $ride->getDateTime()->format('d.m.Y'))
            ->setCanonicalForObject($ride);

        if ($ride->getImageName()) {
            $seoPage->setPreviewPhoto($ride);
        } elseif ($ride->getFeaturedPhoto()) {
            $seoPage->setPreviewPhoto($ride->getFeaturedPhoto());
        } else {
            $seoPage->setPreviewMap($ride);
        }

        if ($ride->getSocialDescription()) {
            $seoPage->setDescription($ride->getSocialDescription());
        } elseif ($ride->getDescription()) {
            $seoPage->setDescription($ride->getDescription());
        }

        /**
         * @var Weather $weather
         */
        $weather = $this->getWeatherRepository()->findCurrentWeatherForRide($ride);

        if ($weather) {
            $weatherForecast = round($weather->getTemperatureEvening()) . ' °C, ' . $weather->getWeatherDescription();
        } else {
            $weatherForecast = null;
        }

        if ($this->getUser()) {
            $participation = $this->getParticipationRepository()->findParticipationForUserAndRide($this->getUser(),
                $ride);
        } else {
            $participation = null;
        }

        return $this->render('Ride/show.html.twig', [
            'city' => $ride->getCity(),
            'ride' => $ride,
            'tracks' => $this->getTrackRepository()->findTracksByRide($ride),
            'photos' => $this->getPhotoRepository()->findPhotosByRide($ride),
            'subrides' => $this->getSubrideRepository()->getSubridesForRide($ride),
            'dateTime' => new \DateTime(),
            'weatherForecast' => $weatherForecast,
            'participation' => $participation,
        ]);
    }
}
