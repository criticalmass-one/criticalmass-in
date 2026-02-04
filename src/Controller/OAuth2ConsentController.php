<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class OAuth2ConsentController extends AbstractController
{
    #[Route('/oauth2/consent', name: 'oauth2_consent', methods: ['POST'])]
    public function consent(Request $request): Response
    {
        $clientId = $request->request->get('client_id');
        $authorizeUrl = $request->request->get('authorize_url');
        $approved = $request->request->get('consent') === 'approve';

        if (null === $clientId || null === $authorizeUrl) {
            throw $this->createNotFoundException();
        }

        $session = $request->getSession();
        $session->set(sprintf('oauth2_consent_%s', $clientId), $approved);

        return $this->redirect($authorizeUrl);
    }
}
