<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Form\Type\UserProfilePhotoType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfilePhotoController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    public function uploadAction(Request $request, UserInterface $user = null, ManagerRegistry $managerRegistry): Response
    {
        $profileUser = $managerRegistry->getRepository(User::class)->find($user->getId());

        $form = $this->createForm(UserProfilePhotoType::class, $profileUser);
        $form->add('submit', SubmitType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->uploadPostAction($request, $form);
        } else {
            return $this->uploadGetAction($request, $form);
        }
    }

    protected function uploadGetAction(Request $request, FormInterface $form): Response
    {
        return $this->render('ProfilePhoto/upload.html.twig', [
            'profilePhotoForm' => $form->createView(),
        ]);
    }

    public function uploadPostAction(Request $request, FormInterface $form): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $profileUser = $form->getData();

            $profileUser->setOwnProfilePhoto(true);

            $this->managerRegistry->getManager()->flush();

            // as we are handling the user interface directly, which is used for the logged in user, Symfony will try to
            // serialize the logged in user object, which will fail for $imageFile of File type, so we set it to null
            // before returning the response
            $profileUser->setImageFile(null);
        }

        return $this->uploadGetAction($request, $form);
    }
}
