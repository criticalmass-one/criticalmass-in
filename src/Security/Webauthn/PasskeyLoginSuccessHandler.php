<?php declare(strict_types=1);

namespace App\Security\Webauthn;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Antwortet auf eine erfolgreiche Passkey-Anmeldung mit dem Ziel, zu dem der Browser
 * weiterspringen soll.
 *
 * Die Anmeldung läuft per fetch(), eine Umleitung des Servers käme also nie im Fenster
 * an. Der Standard-Handler des Bundles antwortet deshalb nur mit `{"status": "ok"}`, und
 * der Stimulus-Controller sprang bisher fest in die Kontoübersicht — auch wenn jemand
 * eigentlich eine geschützte Seite öffnen wollte und dafür erst auf /login gelandet ist.
 * Diese Seite merkt sich die Firewall als Target Path; Magic Link und OAuth führen
 * dorthin zurück, und mit diesem Handler jetzt auch der Passkey.
 */
final class PasskeyLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    use TargetPathTrait;

    // Der Handler bekommt den Firewall-Namen nicht übergeben. Passkeys gibt es nur in
    // `main`, siehe security.yaml.
    private const FIREWALL = 'main';

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $data = [
            'status' => 'ok',
            'errorMessage' => '',
        ];

        if ($request->hasSession()) {
            $session = $request->getSession();
            $targetPath = $this->getTargetPath($session, self::FIREWALL);

            if (null !== $targetPath) {
                $this->removeTargetPath($session, self::FIREWALL);
                $data['redirectUrl'] = $targetPath;
            }
        }

        return new JsonResponse($data);
    }
}
