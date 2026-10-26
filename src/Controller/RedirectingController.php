<?php declare(strict_types=1);

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouterInterface;

class RedirectingController extends AbstractController
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly RouterInterface $router,
    ) {
        parent::__construct($managerRegistry);
    }

    #[Route(
        '/{url}',
        name: 'remove_trailing_slash',
        requirements: ['url' => '.*/$'],
        methods: ['GET'],
        priority: -255
    )]
    public function removeTrailingSlashAction(Request $request): RedirectResponse
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        if (str_starts_with($url, '//') || str_contains($url, '://')) {
            $url = '/';
        }

        $this->sackgasseAusschliessen(rtrim($pathInfo, ' /'));

        return $this->redirect($url, RedirectResponse::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * Bricht ab, wenn hinter dem Ziel gar nichts liegt.
     *
     * Diese Route passt auf jeden Pfad, der auf einen Schraegstrich endet —
     * und genau daraus schliesst Symfony fuer *jeden* unbekannten Pfad, dass
     * es ihn mit angehaengtem Schraegstrich gaebe. Es leitet also /gibt/es/nicht
     * auf /gibt/es/nicht/ um, worauf diese Methode den Schraegstrich wieder
     * abschneidet: eine Schleife, die keinen Fehler wirft und nie endet.
     *
     * Sichtbar wurde sie erst, als die Zahlbedingungen an den Routen dafuer
     * sorgten, dass ein /photo/foo nicht mehr in einem 500er endet, sondern
     * ueberhaupt keine Route mehr trifft.
     */
    private function sackgasseAusschliessen(string $ziel): void
    {
        if ('' === $ziel) {
            return;
        }

        // Bewusst ein schlichter UrlMatcher und nicht der Router: Dessen
        // Matcher leitet selbst um und meldet deshalb fuer jeden Pfad einen
        // Treffer — er wuerde die Schleife bestaetigen statt sie zu erkennen.
        $matcher = new UrlMatcher($this->router->getRouteCollection(), $this->router->getContext());

        try {
            $matcher->match($ziel);
        } catch (RoutingException) {
            throw $this->createNotFoundException(sprintf('Nothing behind %s', $ziel));
        }
    }
}
