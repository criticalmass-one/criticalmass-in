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
     * Nur lokale Pfade, sonst Startseite. Browser entfernen Tabs und Zeilenumbrueche
     * aus URLs, `/\t/host` wuerde also zu `//host` – deshalb keine Steuerzeichen.
     */
    private function safeTarget(Request $request): string
    {
        $target = $request->request->getString('_target');

        if (1 === preg_match('#^/(?![/\\\\])[^\x00-\x20\x7f]*\z#', $target)) {
            return $target;
        }

        return $this->generateUrl('caldera_criticalmass_frontpage');
    }
}
