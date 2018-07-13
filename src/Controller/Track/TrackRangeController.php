<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Event\Track\TrackTrimmedEvent;
use App\Form\Type\TrackRangeType;
use App\Criticalmass\Gps\LatLngListGenerator\SimpleLatLngListGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use App\Entity\Track;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class TrackRangeController extends AbstractController
{
    /**
     * @Security("is_granted('edit', track)")
     * @ParamConverter("track", class="App:Track", options={"id" = "trackId"})
     */
    public function rangeAction(Request $request, Track $track, SimpleLatLngListGenerator $latLngListGenerator, EventDispatcherInterface $eventDispatcher): Response
    {
        $form = $this->createForm(TrackRangeType::class, $track);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->rangePostAction($request, $track, $form, $latLngListGenerator, $eventDispatcher);
        } else {
            return $this->rangeGetAction($request, $track, $form, $latLngListGenerator, $eventDispatcher);
        }
    }

    protected function rangeGetAction(Request $request, Track $track, FormInterface $form, SimpleLatLngListGenerator $latLngListGenerator, EventDispatcherInterface $eventDispatcher): Response
    {
        $latLngListGenerator
            ->loadTrack($track)
            ->execute();

        return $this->render('Track/range.html.twig', [
            'form' => $form->createView(),
            'track' => $track,
            'latLngList' => $latLngListGenerator->getList(),
            'gapWidth' => $this->getParameter('track.gap_width'),
        ]);
    }

    protected function rangePostAction(Request $request, Track $track, FormInterface $form, SimpleLatLngListGenerator $latLngListGenerator, EventDispatcherInterface $eventDispatcher): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Track $track */
            $track = $form->getData();

            $eventDispatcher->dispatch(TrackTrimmedEvent::NAME, new TrackTrimmedEvent($track));
        }

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }
}
