<?php

namespace AppBundle\Controller\Track;

use AppBundle\Criticalmass\UploadValidator\TrackValidator;
use AppBundle\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\TrackValidatorException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Traits\TrackHandlingTrait;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackUploadController extends AbstractController
{
    use TrackHandlingTrait;

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function uploadAction(Request $request, Ride $ride, TrackValidator $trackValidator): Response
    {
        $track = new Track();

        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_upload', [
                'citySlug' => $ride->getCity()->getMainSlugString(),
                'rideDate' => $ride->getFormattedDate(),
            ]))
            ->add('trackFile', VichFileType::class)
            ->getForm();

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->uploadPostAction($request, $track, $ride, $form, $trackValidator);
        } else {
            return $this->uploadGetAction($request, $ride, $form, $trackValidator);
        }
    }

    protected function uploadGetAction(Request $request, Ride $ride, Form $form, TrackValidator $trackValidator): Response
    {
        return $this->render('AppBundle:Track:upload.html.twig', [
            'form' => $form->createView(),
            'ride' => $ride,
            'errorMessage' => null,
        ]);
    }

    public function uploadPostAction(Request $request, Track $track, Ride $ride, Form $form, TrackValidator $trackValidator): Response
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /** @var Track $track */
            $track = $form->getData();

            /* Save the track so the Uploader will place the file at the file system */
            $em->persist($track);

            $trackValidator->loadTrack($track);

            try {
                $trackValidator->validate();
            } catch (TrackValidatorException $e) {
                return $this->render('AppBundle:Track:upload.html.twig', [
                    'form' => $form->createView(),
                    'ride' => $ride,
                    'errorMessage' => $e->getMessage(),
                ]);
            }

            $this->loadTrackProperties($track);

            $track
                ->setRide($ride)
                ->setUser($this->getUser())
                ->setUsername($this->getUser()->getUsername());

            $em->persist($track);
            $em->flush();

            $this->addRideEstimate($track, $ride);
            $this->generateSimpleLatLngList($track);
            $this->generatePolyline($track);

            return $this->redirect($this->generateUrl('caldera_criticalmass_track_view', [
                'trackId' => $track->getId(),
            ]));
        }

        return $this->uploadGetAction($request, $ride, $form, $trackValidator);
    }
}
