<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Event\Track\TrackUploadedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Controller\AbstractController;
use App\Entity\Ride;
use App\Entity\Track;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackUploadController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(
        '/{citySlug}/{rideIdentifier}/addtrack',
        name: 'caldera_criticalmass_track_upload',
        priority: 270
    )]
    public function uploadAction(Request $request, EventDispatcherInterface $eventDispatcher, ObjectRouterInterface $objectRouter, Ride $ride): Response
    {
        $track = new Track();

        $form = $this->createFormBuilder($track)
            ->setAction($objectRouter->generate($ride, 'caldera_criticalmass_track_upload'))
            ->add('trackFile', VichFileType::class)
            ->getForm();

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->managerRegistry->getManager();

                /** @var Track $track */
                $track = $form->getData();

                $track
                    ->setRide($ride)
                    ->setUser($this->getUser())
                    ->setUsername($this->getUser()->getUsername())
                    ->setSource(Track::TRACK_SOURCE_GPX);

                $em->persist($track);
                $em->flush();

                $eventDispatcher->dispatch(new TrackUploadedEvent($track), TrackUploadedEvent::NAME);

                return $this->redirect($objectRouter->generate($track));
            }
        }

        return $this->render('Track/upload.html.twig', [
            'form' => $form->createView(),
            'ride' => $ride,
        ]);
    }
}
