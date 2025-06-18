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
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfilePhotoController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    public function uploadAction(Request $request, UserInterface $user = null): Response
    {
        $user = clone $user; // otherwise doctrine will try to serialize the user object and fail with the File property
        $form = $this->createForm(UserProfilePhotoType::class, $user);
        $form->add('submit', SubmitType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->uploadPostAction($request, $user, $form);
        } else {
            return $this->uploadGetAction($request, $user, $form);
        }
    }

    protected function uploadGetAction(Request $request, UserInterface $user = null, FormInterface $form): Response
    {
        return $this->render('ProfilePhoto/upload.html.twig', [
            'profilePhotoForm' => $form->createView(),
        ]);
    }

    public function uploadPostAction(Request $request, UserInterface $user = null, FormInterface $form): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $user->setOwnProfilePhoto(true);

            $this->managerRegistry->getManager()->flush();
        }

        return $this->uploadGetAction($request, $user, $form);
    }
}
