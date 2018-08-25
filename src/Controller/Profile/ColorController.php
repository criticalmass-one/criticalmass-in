<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Form\Type\ProfileColorType;
use App\Form\Type\UserProfilePhotoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ColorController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function colorAction(Request $request, UserInterface $user = null): Response
    {
        $form = $this->createForm(ProfileColorType::class, $user);
        $form->add('submit', SubmitType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->colorPostAction($request, $user, $form);
        } else {
            return $this->colorGetAction($request, $user, $form);
        }
    }

    protected function colorGetAction(Request $request, UserInterface $user = null, FormInterface $form): Response
    {
        return $this->render('ProfileColor/color.html.twig', [
            'profileColorForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    public function colorPostAction(Request $request, UserInterface $user = null, FormInterface $form): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->colorGetAction($request, $user, $form);
    }
}
