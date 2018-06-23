<?php declare(strict_types=1);

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

class TrackController extends AbstractController
{
    use TrackHandlingTrait;

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function uploadAction(Request $request, Ride $ride, $embed = false): Response
    {
        $track = new Track();

        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_upload', [
                'citySlug' => $ride->getCity()->getMainSlugString(),
                'rideDate' => $ride->getFormattedDate(),
            ]))
            ->add('trackFile', VichFileType::class)
            ->getForm();

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->uploadPostAction($request, $track, $ride, $form, $embed);
        } else {
            return $this->uploadGetAction($request, $ride, $form, $embed);
        }
    }

    protected function uploadGetAction(Request $request, Ride $ride, Form $form, $embed): Response
    {
        return $this->render('AppBundle:Track:upload.html.twig', [
            'form' => $form->createView(),
            'ride' => $ride,
            'errorMessage' => null,
        ]);
    }

    public function uploadPostAction(Request $request, Track $track, Ride $ride, Form $form, $embed): Response
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
                return $this->render('AppBundle:Track:upload.html.twig', [
                    'form' => $form->createView(),
                    'ride' => $ride,
                    'errorMessage' => $e->getMessage(),
                ]);
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

            return $this->redirect($this->generateUrl('caldera_criticalmass_track_view', [
                'trackId' => $track->getId()
            ]));
        }

        return $this->render('AppBundle:Track:upload.html.twig', [
            'form' => $form->createView(),
            'ride' => $ride,
            'errorMessage' => null,
        ]);
    }

    /**
     * @Security("is_granted('view', track)")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function viewAction(Track $track): Response
    {
        return $this->render('AppBundle:Track:view.html.twig', [
            'track' => $track,
            'nextTrack' => $this->getTrackRepository()->getNextTrack($track),
            'previousTrack' => $this->getTrackRepository()->getPreviousTrack($track),
        ]);
    }

    /**
     * @Security("is_granted('download', track)")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function downloadAction(Track $track): Response
    {
        $path = $this->getParameter('kernel.root_dir');
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $filename = $helper->asset($track, 'trackFile');

        $trackContent = file_get_contents($path . '/../web' . $filename);

        $response = new Response();

        $response->headers->add([
            'Content-disposition' => 'attachment; filename=track.gpx',
            'Content-type',
            'text/plain',
        ]);

        $response->setContent($trackContent);

        return $response;
    }
}
