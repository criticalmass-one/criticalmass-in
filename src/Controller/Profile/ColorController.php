<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Event\User\UserColorChangedEvent;
use App\Form\Type\ProfileColorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ColorController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    public function colorAction(Request $request, EventDispatcherInterface $eventDispatcher, UserInterface $user = null): Response
    {
        $form = $this->createForm(ProfileColorType::class, $user);
        $form->add('submit', SubmitType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->colorPostAction($request, $user, $form, $eventDispatcher);
        } else {
            return $this->colorGetAction($request, $user, $form, $eventDispatcher);
        }
    }

    protected function colorGetAction(Request $request, UserInterface $user = null, FormInterface $form, EventDispatcherInterface $eventDispatcher): Response
    {
        return $this->render('ProfileColor/color.html.twig', [
            'profileColorForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    public function colorPostAction(Request $request, UserInterface $user = null, FormInterface $form, EventDispatcherInterface $eventDispatcher): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->managerRegistry->getManager()->flush();

            $eventDispatcher->dispatch(new UserColorChangedEvent($user), UserColorChangedEvent::NAME);
        }

        return $this->colorGetAction($request, $user, $form, $eventDispatcher);
    }
}
