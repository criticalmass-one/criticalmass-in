<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Event\User\UserColorChangedEvent;
use App\Form\Type\ProfileColorType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ColorController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(
        '/profile/color',
        name: 'criticalmass_user_profile_color',
        priority: 180
    )]
    public function colorAction(
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        ?UserInterface $user = null
    ): Response {
        $form = $this->createForm(ProfileColorType::class, $user);
        $form->add('submit', SubmitType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->colorPostAction($request, $form, $eventDispatcher, $user);
        }

        return $this->colorGetAction($request, $form, $eventDispatcher, $user);
    }

    protected function colorGetAction(
        Request $request,
        FormInterface $form,
        EventDispatcherInterface $eventDispatcher,
        ?UserInterface $user = null
    ): Response {
        return $this->render('ProfileColor/color.html.twig', [
            'profileColorForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    public function colorPostAction(
        Request $request,
        FormInterface $form,
        EventDispatcherInterface $eventDispatcher,
        ?UserInterface $user = null
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->managerRegistry->getManager()->flush();

            $eventDispatcher->dispatch(new UserColorChangedEvent($user), UserColorChangedEvent::NAME);
        }

        return $this->colorGetAction($request, $form, $eventDispatcher, $user);
    }
}
