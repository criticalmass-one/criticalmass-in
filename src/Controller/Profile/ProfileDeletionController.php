<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Criticalmass\Profile\Deletion\UserDeleterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileDeletionController extends AbstractController
{
    /**
     * @Security("is_granted('cancel', user)")
     */
    public function deleteAction(Request $request, UserInterface $user, UserDeleterInterface $userDeleter): Response
    {
        if ($request->isMethod(Request::METHOD_GET)) {
            return $this->deleteGetAction($request, $user, $userDeleter);
        }
    }

    protected function deleteGetAction(Request $request, UserInterface $user, UserDeleterInterface $userDeleter): Response
    {
        $userDeleter->deleteUser($user);

        return $this->render('ProfileDeletion/cancel.html.twig');
    }
}
