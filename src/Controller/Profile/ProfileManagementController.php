<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\Participation;
use App\Entity\Photo;
use App\Entity\Track;
use App\Form\Type\UsernameType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileManagementController extends AbstractController
{
    //public function __construct(private readonly UserManagerInterface $userManager)
    //{
    //}
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function manageAction(UserInterface $user = null): Response
    {
        $participationCounter = $this->getDoctrine()->getRepository(Participation::class)->countByUser($user);
        $trackCounter = $this->getDoctrine()->getRepository(Track::class)->countByUser($user);
        $photoCounter = $this->getDoctrine()->getRepository(Photo::class)->countByUser($user);

        return $this->render('ProfileManagement/manage.html.twig', [
            'participationCounter' => $participationCounter,
            'trackCounter' => $trackCounter,
            'photoCounter' => $photoCounter,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function editUsernameAction(
        Request $request,
        ManagerRegistry $managerRegistry,
        UserInterface $user = null
    ): Response {
        $usernameForm = $this->createForm(UsernameType::class, $user, [
            'action' => $this->generateUrl('criticalmass_user_usermanagement_editusername')
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            $usernameForm->handleRequest($request);

            if ($usernameForm->isSubmitted() && $usernameForm->isValid()) {
                try {
                    $managerRegistry->getManager()->flush();

                    $this->addFlash('success',
                        sprintf('Deine neuer Benutzername wurde gespeichert. Du heißt jetzt %s!',
                            $user->getUsername()
                        ));

                    return $this->redirectToRoute('criticalmass_user_usermanagement');
                } catch (UniqueConstraintViolationException $exception) {
                    $error = new FormError('Dieser Benutzername ist bereits vergeben.');

                    $usernameForm->get('username')->addError($error);
                }
            }
        }

        return $this->render('ProfileManagement/edit_username.html.twig', [
            'usernameForm' => $usernameForm->createView(),
        ]);
    }
}
