<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Event\Track\TrackTimeEvent;
use App\Controller\AbstractController;
use App\Entity\Track;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrackTimeController extends AbstractController
{
    #[IsGranted('edit', 'track')]
    #[Route(
        '/track/time/{id}',
        name: 'caldera_criticalmass_track_time',
        priority: 270
    )]
    public function timeAction(
        Request $request,
        ObjectRouterInterface $objectRouter,
        EventDispatcherInterface $eventDispatcher,
        GpxServiceInterface $gpxService,
        Track $track
    ): Response {
        $form = $this->createFormBuilder($track)
            ->setAction($objectRouter->generate($track, 'caldera_criticalmass_track_time'))
            ->add('startDate', DateType::class)
            ->add('startTime', TimeType::class)
            ->getForm();

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->timePostAction($request, $eventDispatcher, $gpxService, $track, $form);
        }

        return $this->timeGetAction($track, $form);
    }

    protected function timeGetAction(Track $track, FormInterface $form): Response
    {
        return $this->render('Track/time.html.twig', [
            'form' => $form->createView(),
            'track' => $track,
        ]);
    }

    protected function timePostAction(
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        GpxServiceInterface $gpxService,
        Track $track,
        FormInterface $form
    ): Response {
        // catch the old dateTime before it is overridden by the form submit
        $oldDateTime = $track->getStartDateTime();

        // now get the new values
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Track $newTrack */
            $newTrack = $form->getData();

            $interval = $newTrack->getStartDateTime()->diff($oldDateTime);

            $gpxService->shiftTimeAndSave($newTrack, $interval);

            $eventDispatcher->dispatch(new TrackTimeEvent($track), TrackTimeEvent::NAME);
        }

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }
}
