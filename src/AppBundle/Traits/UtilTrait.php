<?php

namespace AppBundle\Traits;

use AppBundle\Entity\City;
use AppBundle\Entity\CitySlug;
use AppBundle\Entity\Ride;
use AppBundle\HtmlMetadata\Metadata;
use AppBundle\Router\ObjectRouter;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\SeoBundle\Seo\SeoPage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait UtilTrait
{
    /**
     * Returns a city entity identified by its slug.
     *
     * @param $citySlug
     * @return City
     * @throws NotFoundHttpException
     */
    protected function getCityBySlug($citySlug): City
    {
        /** @var CitySlug $citySlug */
        $citySlug = $this->getCitySlugRepository()->findOneBySlug($citySlug);

        if ($citySlug) {
            return $citySlug->getCity();
        } else {
            throw new NotFoundHttpException();
        }
    }

    protected function getSeoPage(): SeoPage
    {
        return $this->get('sonata.seo.page.default');
    }

    protected function getCheckedCity($citySlug): City
    {
        $city = $this->getCityBySlug($citySlug);

        if (!$city) {
            throw new NotFoundHttpException(
                'Wir haben leider keine Stadt in der Datenbank, die sich mit '.$citySlug.' identifiziert.'
            );
        }

        return $city;
    }

    /**
     * Returns a ride entity for a city entity and a datetime parameter. Will throw an exception if the ride does not exist.
     *
     * @param City $city
     * @param \DateTime $rideDateTime
     * @throws NotFoundHttpException
     * @return Ride
     */
    protected function getCheckedRide(City $city, \DateTime $rideDateTime): Ride
    {
        /** @var Ride $ride */
        $ride = $this->getRideRepository()->findCityRideByDate($city, $rideDateTime);

        if (!$ride) {
            throw new NotFoundHttpException(
                'Wir haben leider keine Tour in '.$city->getCity().' am '.$rideDateTime->format('d. m. Y').' gefunden.'
            );
        }

        return $ride;
    }

    protected function getCheckedDateTime(string $dateTime): \DateTime
    {
        try {
            $dateTime = new \DateTime($dateTime);
        } catch (\Exception $e) {
            throw new NotFoundHttpException(
                'Mit diesem Datum können wir leider nichts anfange. Bitte gib ein Datum im Format YYYY-MM-DD an.'
            );
        }

        return $dateTime;
    }

    protected function getCheckedCitySlugRideDateRide(string $citySlug, string $dateTime): Ride
    {
        /** @var City $city */
        $city = $this->getCheckedCity($citySlug);

        /** @var \DateTime $rideDateTime */
        $rideDateTime = $this->getCheckedDateTime($dateTime);

        /** @var Ride $ride */
        $ride = $this->getCheckedRide($city, $rideDateTime);

        return $ride;
    }

    protected function getSession(): Session
    {
        $session = new Session();

        return $session;
    }

    protected function generateObjectUrl($object, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        /** @var ObjectRouter $router */
        $router = $this->get('caldera.criticalmass.routing.object_router');

        $url = $router->generate($object, $referenceType);

        return $url;
    }

    protected function redirectToObject($object, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $url = $this->generateObjectUrl($object, $referenceType);

        return $this->redirect($url);
    }

    protected function getManager(): EntityManagerInterface
    {
        return $this->getDoctrine()->getManager();
    }

    protected function saveReferer(Request $request): string
    {
        $referer = $request->headers->get('referer');

        $this->getSession()->set('referer', $referer);

        return $referer;
    }

    protected function getSavedReferer(): ?string
    {
        return $this->getSession()->get('referer');
    }

    protected function createRedirectResponseForSavedReferer(): RedirectResponse
    {
        $referer = $this->getSavedReferer();

        if (!$referer) {
            throw new \Exception('No saved referer found to redirect to.');
        }

        return new RedirectResponse($referer);
    }
}
