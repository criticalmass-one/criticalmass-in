<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Track;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Bundle\AppBundle\Traits\TrackHandlingTrait;
use Criticalmass\Component\Gps\TrackTimeShift\TrackTimeShift;
use Criticalmass\Bundle\AppBundle\UploadValidator\TrackValidator;
use Criticalmass\Bundle\AppBundle\UploadValidator\UploadValidatorException\TrackValidatorException\TrackValidatorException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackUploadController extends AbstractController
{
    use TrackHandlingTrait;

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function uploadAction(Request $request, $citySlug, $rideDate, $embed = false)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);
        $track = new Track();

        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_upload',
                [
                    'citySlug' => $ride->getCity()->getMainSlugString(),
                    'rideDate' => $ride->getFormattedDate()
                ]))
            ->add('trackFile', VichFileType::class)
            ->getForm();

        if ('POST' == $request->getMethod()) {
            return $this->uploadPostAction($request, $track, $ride, $form, $embed);
        } else {
            return $this->uploadGetAction($request, $ride, $form, $embed);
        }
    }

    protected function uploadGetAction(Request $request, Ride $ride, Form $form, $embed)
    {
        return $this->render('AppBundle:Track:upload.html.twig',
            [
                'form' => $form->createView(),
                'ride' => $ride,
                'errorMessage' => null
            ]);
    }

    public function uploadPostAction(Request $request, Track $track, Ride $ride, Form $form, $embed)
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /**
             * @var Track $track
             */
            $track = $form->getData();

            /* Save the track so the Uploader will place the file at the file system */
            $em->persist($track);

            /**
             * @var TrackValidator $trackValidator
             */
            $trackValidator = $this->get('caldera.criticalmass.uploadvalidator.track');
            $trackValidator->loadTrack($track);

            try {
                $trackValidator->validate();
            } catch (TrackValidatorException $e) {
                return $this->render(
                    'AppBundle:Track:upload.html.twig',
                    [
                        'form' => $form->createView(),
                        'ride' => $ride,
                        'errorMessage' => $e->getMessage()
                    ]
                );
            }

            $this->loadTrackProperties($track);

            $track->setRide($ride);
            $track->setUser($this->getUser());
            $track->setUsername($this->getUser()->getUsername());

            $em->persist($track);
            $em->flush();

            $this->addRideEstimate($track, $ride);
            $this->generateSimpleLatLngList($track);
            $this->generatePolyline($track);

            return $this->redirect($this->generateUrl('caldera_criticalmass_track_view',
                ['trackId' => $track->getId()]));
        }

        return $this->render(
            'AppBundle:Track:upload.html.twig',
            [
                'form' => $form->createView(),
                'ride' => $ride,
                'errorMessage' => null
            ]
        );
    }
}
