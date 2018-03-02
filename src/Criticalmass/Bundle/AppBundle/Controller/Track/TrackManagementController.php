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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackManagementController extends AbstractController
{
    use TrackHandlingTrait;

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction()
    {
        /**
         * @var array Track
         */
        $tracks = $this->getTrackRepository()->findBy(
            [
                'user' => $this->getUser()->getId(),
                'deleted' => false
            ],
            [
                'startDateTime' => 'DESC'
            ]
        );

        return $this->render('AppBundle:Track:list.html.twig',
            array(
                'tracks' => $tracks
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function downloadAction(Request $request, UserInterface $user, int $trackId): Response
    {
        $track = $this->getCredentialsCheckedTrack($user, $trackId);

        $trackContent = file_get_contents($this->getTrackFilename($track));

        $response = new Response();

        $response->headers->add([
            'Content-disposition' => 'attachment; filename=track.gpx',
            'Content-type',
            'text/plain'
        ]);

        $response->setContent($trackContent);

        return $response;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function toggleAction(Request $request, UserInterface $user, int $trackId): Response
    {
        $track = $this->getCredentialsCheckedTrack($user, $trackId);

        $track->setEnabled(!$track->getEnabled());

        $this->getManager()->flush();

        $this->get('caldera.criticalmass.statistic.rideestimate.track')->calculateEstimates($track->getRide());

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction(Request $request, UserInterface $user, int $trackId): Response
    {
        $track = $this->getCredentialsCheckedTrack($user, $trackId);

        $track->setDeleted(true);

        $this->getManager()->flush();

        $this->get('caldera.criticalmass.statistic.rideestimate.track')->calculateEstimates($track->getRide());

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function rangeAction(Request $request, UserInterface $user, int $trackId): Response
    {
        $track = $this->getCredentialsCheckedTrack($user, $trackId);

        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_range',
                [
                    'trackId' => $track->getId()
                ]
            ))
            ->add('startPoint', HiddenType::class)
            ->add('endPoint', HiddenType::class)
            ->getForm();

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->rangePostAction($request, $track, $form);
        } else {
            return $this->rangeGetAction($request, $track, $form);
        }
    }

    protected function rangeGetAction(Request $request, Track $track, FormInterface $form): Response
    {
        $llag = $this->container->get('caldera.criticalmass.gps.latlnglistgenerator.simple');
        $llag
            ->loadTrack($track)
            ->execute();

        return $this->render('AppBundle:Track:range.html.twig',
            [
                'form' => $form->createView(),
                'track' => $track,
                'latLngList' => $llag->getList(),
                'gapWidth' => $this->getParameter('track.gap_width')
            ]
        );
    }

    protected function rangePostAction(Request $request, Track $track, FormInterface $form): Response
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

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function timeAction(Request $request, UserInterface $user, int $trackId): Response
    {
        $track = $this->getCredentialsCheckedTrack($user, $trackId);

        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl(
                'caldera_criticalmass_track_time',
                [
                    'trackId' => $track->getId()
                ]
            ))
            ->add('startDate', DateType::class)
            ->add('startTime', TimeType::class)
            ->getForm();

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->timePostAction($request, $track, $form);
        } else {
            return $this->timeGetAction($request, $track, $form);
        }
    }

    protected function timeGetAction(Request $request, Track $track, FormInterface $form): Response
    {
        return $this->render('AppBundle:Track:time.html.twig',
            [
                'form' => $form->createView(),
                'track' => $track,
            ]
        );
    }

    protected function timePostAction(Request $request, Track $track, FormInterface $form): Response
    {
        // catch the old dateTime before it is overridden by the form submit
        $oldDateTime = $track->getStartDateTime();

        // now get the new values
        $form->handleRequest($request);

        if ($form->isValid()) {
            /**
             * @var Track $newTrack
             */
            $newTrack = $form->getData();

            $interval = $newTrack->getStartDateTime()->diff($oldDateTime);

            /**
             * @var TrackTimeShift $tts
             */
            $tts = $this->get('caldera.criticalmass.gps.timeshift.track');
            $tts
                ->loadTrack($newTrack)
                ->shift($interval)
                ->saveTrack();

            $this->updateTrackProperties($track);
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    protected function getCredentialsCheckedTrack(UserInterface $user, int $trackId): Track
    {
        /**
         * @var Track $track
         */
        $track = $this->getTrackRepository()->find($trackId);

        if (!$track) {
            throw $this->createNotFoundException();
        }

        if ($track->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        return $track;
    }

    protected function getTrackFilename(Track $track): string
    {
        $rootDirectory = $this->getParameter('kernel.root_dir');
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $filename = $helper->asset($track, 'trackFile');

        return $rootDirectory . '/../web' . $filename;
    }
}
