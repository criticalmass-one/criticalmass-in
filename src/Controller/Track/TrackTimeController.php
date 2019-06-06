<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Criticalmass\Geo\TimeShifter\TrackTimeShifterInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Event\Track\TrackTimeEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use App\Entity\Track;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackTimeController extends AbstractController
{
    /**
     * @Security("is_granted('edit', track)")
     * @ParamConverter("track", class="App:Track", options={"id" = "trackId"})
     */
    public function timeAction(Request $request, ObjectRouterInterface $objectRouter, EventDispatcherInterface $eventDispatcher, Track $track, TrackTimeShifterInterface $trackTimeshift): Response
    {
        $form = $this->createFormBuilder($track)
            ->setAction($objectRouter->generate($track, 'caldera_criticalmass_track_time'))
            ->add('startDate', DateType::class)
            ->add('startTime', TimeType::class)
            ->getForm();

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->timePostAction($request, $eventDispatcher, $track, $form, $trackTimeshift);
        }

        return $this->timeGetAction($request, $eventDispatcher, $track, $form, $trackTimeshift);
    }

    protected function timeGetAction(Request $request, EventDispatcherInterface $eventDispatcher, Track $track, FormInterface $form, TrackTimeShifterInterface $trackTimeshift): Response
    {
        return $this->render('Track/time.html.twig', [
            'form' => $form->createView(),
            'track' => $track,
        ]);
    }

    protected function timePostAction(Request $request, EventDispatcherInterface $eventDispatcher, Track $track, FormInterface $form, TrackTimeShifterInterface $trackTimeshift): Response
    {
        // catch the old dateTime before it is overridden by the form submit
        $oldDateTime = $track->getStartDateTime();

        // now get the new values
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Track $newTrack */
            $newTrack = $form->getData();

            $interval = $newTrack->getStartDateTime()->diff($oldDateTime);

            $trackTimeshift
                ->loadTrack($newTrack)
                ->shift($interval)
                ->saveTrack();

            $eventDispatcher->dispatch(TrackTimeEvent::NAME, new TrackTimeEvent($track));
        }

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }
}
