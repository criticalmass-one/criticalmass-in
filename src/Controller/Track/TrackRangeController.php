<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Criticalmass\Geo\LatLngListGenerator\SimpleLatLngListGenerator;
use App\Entity\Track;
use App\Event\Track\TrackTrimmedEvent;
use App\Form\Type\TrackRangeType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrackRangeController extends AbstractController
{
    public function __construct(private readonly string $gapWidth)
    {

    }

    #[IsGranted('edit', 'track')]
    #[Route('/track/range/{id}', name: 'caldera_criticalmass_track_range', priority: 270)]
    public function rangeAction(Request $request, Track $track, SimpleLatLngListGenerator $latLngListGenerator, EventDispatcherInterface $eventDispatcher): Response
    {
        $form = $this->createForm(TrackRangeType::class, $track);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->rangePostAction($request, $track, $form, $latLngListGenerator, $eventDispatcher);
        }

        return $this->rangeGetAction($request, $track, $form, $latLngListGenerator, $eventDispatcher);
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
        ]);
    }

    protected function rangePostAction(Request $request, Track $track, FormInterface $form, SimpleLatLngListGenerator $latLngListGenerator, EventDispatcherInterface $eventDispatcher): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Track $track */
            $track = $form->getData();

            $track
                // assure that end point does not exceed number of points due to round problems in javascript
                ->setEndPoint(min($track->getEndPoint(), $track->getPoints()))
                // this may not be done in TrackEventSubscriber as the events are sometimes triggered automatically
                ->setReviewed(true)
            ;

            $eventDispatcher->dispatch(new TrackTrimmedEvent($track), TrackTrimmedEvent::NAME);
        }

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }
}
