<?php declare(strict_types=1);

namespace AppBundle\Controller\Profile;

use AppBundle\Controller\AbstractController;
use AppBundle\Form\Type\UserProfilePhotoType;
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
    public function uploadAction(Request $request, UserInterface $user): Response
    {
        $form = $this->createForm(UserProfilePhotoType::class, $user);
        $form->add('submit', SubmitType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->uploadPostAction($request, $user, $form);
        } else {
            return $this->uploadGetAction($request, $user, $form);
        }
    }

    protected function uploadGetAction(Request $request, UserInterface $user, FormInterface $form): Response
    {
        return $this->render('AppBundle:ProfilePhoto:upload.html.twig', [
            'profilePhotoForm' => $form->createView(),
        ]);
    }

    public function uploadPostAction(Request $request, UserInterface $user, FormInterface $form): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->uploadGetAction($request, $user, $form);
    }
}
