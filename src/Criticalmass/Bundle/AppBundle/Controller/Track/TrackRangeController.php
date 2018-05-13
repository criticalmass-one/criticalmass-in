<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Controller\Track;

use Criticalmass\Component\Gps\LatLngListGenerator\SimpleLatLngListGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Bundle\AppBundle\Traits\TrackHandlingTrait;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class TrackRangeController extends AbstractController
{
    use TrackHandlingTrait;

    /**
     * @Security("is_granted('edit', track)")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function rangeAction(Request $request, UserInterface $user, Track $track, SimpleLatLngListGenerator $latLngListGenerator): Response
    {
        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_range', [
                'trackId' => $track->getId()
            ]))
            ->add('startPoint', HiddenType::class)
            ->add('endPoint', HiddenType::class)
            ->getForm();

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->rangePostAction($request, $track, $form, $latLngListGenerator);
        } else {
            return $this->rangeGetAction($request, $track, $form, $latLngListGenerator);
        }
    }

    protected function rangeGetAction(Request $request, Track $track, FormInterface $form, SimpleLatLngListGenerator $latLngListGenerator): Response
    {
        $latLngListGenerator
            ->loadTrack($track)
            ->execute();

        return $this->render('AppBundle:Track:range.html.twig', [
            'form' => $form->createView(),
            'track' => $track,
            'latLngList' => $latLngListGenerator->getList(),
            'gapWidth' => $this->getParameter('track.gap_width')
        ]);
    }

    protected function rangePostAction(Request $request, Track $track, FormInterface $form, SimpleLatLngListGenerator $latLngListGenerator): Response
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $track = $form->getData();

            $this->generatePolyline($track);
            $this->saveLatLngList($track);
            $this->updateTrackProperties($track);
            $this->calculateRideEstimates($track);
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }
}
