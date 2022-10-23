<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Form\Type\UserProfilePhotoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfilePhotoController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function uploadAction(Request $request, UserInterface $user = null): Response
    {
        $form = $this->createForm(UserProfilePhotoType::class, $user);
        $form->add('submit', SubmitType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->uploadPostAction($request, $form, $user);
        } else {
            return $this->uploadGetAction($request, $form, $user);
        }
    }

    protected function uploadGetAction(Request $request, FormInterface $form, UserInterface $user = null): Response
    {
        return $this->render('ProfilePhoto/upload.html.twig', [
            'profilePhotoForm' => $form->createView(),
        ]);
    }

    public function uploadPostAction(Request $request, FormInterface $form, UserInterface $user = null): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $user->setOwnProfilePhoto(true);

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->uploadGetAction($request, $form, $user);
    }
}
