<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Form\Type\DisconnectSocialNetworkLoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class SocialNetworkController extends AbstractController
{
    public function __construct(private readonly UserManagerInterface $userManager)
    {

    }
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function disconnectLoginAction(Request $request, UserInterface $user = null): Response
    {
        $form = $this->createForm(DisconnectSocialNetworkLoginType::class, $user);
        $form->add('submit', SubmitType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->uploadPostAction($request, $user, $form);
        } else {
            return $this->uploadGetAction($request, $user, $form);
        }
    }

    protected function uploadGetAction(Request $request, UserInterface $user = null, FormInterface $form): Response
    {
        return $this->render('ProfileManagement/disconnect_social_login.html.twig', [
            'disconnectForm' => $form->createView(),
        ]);
    }

    public function uploadPostAction(Request $request, UserInterface $user = null, FormInterface $form): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $this->userManager->updateUser($user);
        }

        return $this->uploadGetAction($request, $user, $form);
    }
}
