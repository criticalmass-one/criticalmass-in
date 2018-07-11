<?php

namespace AppBundle\Traits;

use AppBundle\Criticalmass\Router\ObjectRouter;
use AppBundle\EntityInterface\RouteableInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait UtilTrait
{
    /** @deprecated  */
    protected function getSession(): Session
    {
        $session = new Session();

        return $session;
    }

    protected function generateObjectUrl(RouteableInterface $object, string $routeName = null, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        /** @var ObjectRouter $router */
        $router = $this->get(ObjectRouter::class);

        $url = $router->generate($object, $routeName, $parameters, $referenceType);

        return $url;
    }

    protected function redirectToObject(RouteableInterface $object, string $routeName = null, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $url = $this->generateObjectUrl($object, $routeName, $parameters, $referenceType);

        return $this->redirect($url);
    }

    /** @deprecated  */
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
