<?php declare(strict_types=1);

namespace App\Controller;

use App\Design\DesignChoice;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Schaltet zwischen der bisherigen und der neuen Ansicht um.
 *
 * Gäste bekommen die Wahl als Cookie, angemeldete Nutzer zusätzlich am Konto, damit
 * sie auf allen Geräten gilt. Das Cookie wird auch für Angemeldete gesetzt: Nach dem
 * Abmelden bleibt das Gerät so bei der gewählten Ansicht.
 *
 * Das Token ist zustandslos (framework.csrf_protection.stateless_token_ids), damit das
 * Formular in der Fußzeile Gästen keine Sitzung anlegt.
 */
class DesignController extends AbstractController
{
    public const string CSRF_INTENT = 'design-switch';

    private const int COOKIE_LIFETIME = 31536000;

    #[Route('/ansicht', name: 'criticalmass_design_switch', methods: ['POST'], priority: 240)]
    public function switchAction(Request $request, DesignChoice $designChoice): Response
    {
        if (!$designChoice->isAvailable()) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid(self::CSRF_INTENT, (string) $request->request->get('_token'))) {
            // Kein AccessDenied: das schickte Gäste auf die Anmeldeseite.
            throw new BadRequestHttpException('Ungültiges Formular-Token.');
        }

        $design = $request->request->getString('design');

        if (!in_array($design, [DesignChoice::V1, DesignChoice::V2], true)) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();

        if ($user instanceof User) {
            $user->setDesignV2(DesignChoice::V2 === $design);
            $this->managerRegistry->getManager()->flush();
        }

        $this->addFlash('success', DesignChoice::V2 === $design
            ? 'Du siehst jetzt die neue Ansicht. Seiten, die es darin noch nicht gibt, erscheinen weiter wie bisher.'
            : 'Du siehst jetzt wieder die bisherige Ansicht.'
        );

        $response = new RedirectResponse($this->safeTarget($request));
        $response->headers->setCookie(Cookie::create(DesignChoice::COOKIE)
            ->withValue($design)
            ->withExpires(time() + self::COOKIE_LIFETIME)
            ->withPath('/')
            ->withSecure($request->isSecure())
            ->withHttpOnly(true)
            ->withSameSite(Cookie::SAMESITE_LAX)
        );

        return $response;
    }

    /**
     * Zurück auf die Seite, von der umgeschaltet wurde – aber nur innerhalb dieser
     * Website. Alles andere (fremde Hosts, `//host`, `/\host`) führt zur Startseite.
     */
    private function safeTarget(Request $request): string
    {
        $target = $request->request->getString('_target');

        if (1 === preg_match('#^/(?![/\\\\])#', $target)) {
            return $target;
        }

        return $this->generateUrl('caldera_criticalmass_frontpage');
    }
}
