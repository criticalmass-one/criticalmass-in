<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    const TEMPLATE_MODE_FULL = 1;
    const TEMPLATE_MODE_FORM = 2;
    const TEMPLATE_MODE_MODAL = 3;

    public function loginAction(Request $request, int $templateMode = self::TEMPLATE_MODE_FULL): Response
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();

        return $this->renderLogin([
                'last_username' => $lastUsername,
                'error' => $error,
                'csrf_token' => $csrfToken
            ],
            $templateMode
        );
    }

    protected function renderLogin(array $data, int $templateMode = null): Response
    {
        $templateName = '';

        switch ($templateMode) {
            case self::TEMPLATE_MODE_FULL:
                $templateName = 'login.html.twig';
                break;
            case self::TEMPLATE_MODE_FORM:
                $templateName = 'loginForm.html.twig';
                break;
            case self::TEMPLATE_MODE_MODAL:
                $templateName = 'loginModalForm.html.twig';
                break;
        }

        return $this->render(sprintf('bundles/FOSUserBundle/Security/%s', $templateName), $data);
    }

    public function loginFormModalAction(Request $request): Response
    {
        return $this->loginAction($request, self::TEMPLATE_MODE_MODAL);
    }

    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
