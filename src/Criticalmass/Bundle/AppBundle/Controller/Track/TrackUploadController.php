<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Controller\Track;

use Criticalmass\Bundle\AppBundle\Event\Track\TrackUploadedEvent;
use Criticalmass\Component\UploadValidator\TrackValidator;
use Criticalmass\Component\UploadValidator\UploadValidatorException\TrackValidatorException\TrackValidatorException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackUploadController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function uploadAction(Request $request, EventDispatcherInterface $eventDispatcher, Ride $ride, TrackValidator $trackValidator): Response
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
            return $this->uploadPostAction($request, $eventDispatcher, $track, $ride, $form, $trackValidator);
        } else {
            return $this->uploadGetAction($request, $eventDispatcher, $ride, $form, $trackValidator);
        }
    }

    protected function uploadGetAction(Request $request, EventDispatcherInterface $eventDispatcher, Ride $ride, FormInterface $form, TrackValidator $trackValidator): Response
    {
        return $this->render('AppBundle:Track:upload.html.twig', [
            'form' => $form->createView(),
            'ride' => $ride,
            'errorMessage' => null,
        ]);
    }

    public function uploadPostAction(Request $request, EventDispatcherInterface $eventDispatcher, Track $track, Ride $ride, FormInterface $form, TrackValidator $trackValidator): Response
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

            $track
                ->setRide($ride)
                ->setUser($this->getUser())
                ->setUsername($this->getUser()->getUsername());

            $em->persist($track);
            $em->flush();

            $eventDispatcher->dispatch(TrackUploadedEvent::NAME, new TrackUploadedEvent($track));

            return $this->redirect($this->generateUrl('caldera_criticalmass_track_view', [
                'trackId' => $track->getId(),
            ]));
        }

        return $this->uploadGetAction($request, $eventDispatcher, $ride, $form, $trackValidator);
    }
}
