<?php declare(strict_types=1);

namespace App\Controller\Profile;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileDeletionController extends Controller
{
    /**
     * @Security("is_granted('cancel', user)")
     */
    public function deleteAction(Request $request, UserInterface $user): Response
    {
        if ($request->isMethod(Request::METHOD_GET)) {
            return $this->deleteGetAction($request, $user);
        }
    }

    protected function deleteGetAction(Request $request, UserInterface $user): Response
    {
        return $this->render('ProfileDeletion/cancel.html.twig');
    }
}
