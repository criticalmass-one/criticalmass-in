<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Criticalmass\Fit\FitConverter\FitConverterInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Event\Track\TrackUploadedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use App\Criticalmass\UploadValidator\TrackValidator;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\TrackValidatorException;
use App\Controller\AbstractController;
use App\Entity\Ride;
use App\Entity\Track;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackUploadController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(
        '/{citySlug}/{rideIdentifier}/addtrack',
        name: 'caldera_criticalmass_track_upload',
        priority: 270
    )]
    public function uploadAction(
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter,
        Ride $ride,
        TrackValidator $trackValidator,
        FitConverterInterface $fitConverter,
        UploadFakerInterface $uploadFaker,
    ): Response {
        $track = new Track();

        $form = $this->createFormBuilder($track)
            ->setAction($objectRouter->generate($ride, 'caldera_criticalmass_track_upload'))
            ->add('trackFile', VichFileType::class)
            ->getForm();

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->uploadPostAction($request, $eventDispatcher, $objectRouter, $track, $ride, $form, $trackValidator, $fitConverter, $uploadFaker);
        } else {
            return $this->uploadGetAction($ride, $form);
        }
    }

    protected function uploadGetAction(Ride $ride, FormInterface $form): Response
    {
        return $this->render('Track/upload.html.twig', [
            'form' => $form->createView(),
            'ride' => $ride,
            'errorMessage' => null,
        ]);
    }

    public function uploadPostAction(
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter,
        Track $track,
        Ride $ride,
        FormInterface $form,
        TrackValidator $trackValidator,
        FitConverterInterface $fitConverter,
        UploadFakerInterface $uploadFaker,
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            /** @var Track $track */
            $track = $form->getData();

            /* Save the track so the Uploader will place the file at the file system */
            $em->persist($track);

            $trackValidator->loadTrack($track);

            try {
                $trackValidator->validate();
            } catch (TrackValidatorException $e) {
                return $this->render('Track/upload.html.twig', [
                    'form' => $form->createView(),
                    'ride' => $ride,
                    'errorMessage' => $e->getMessage(),
                ]);
            }

            $isFitFile = $trackValidator->isFitFile();

            if ($isFitFile) {
                try {
                    $trackFilename = $track->getTrackFilename();
                    $trackDirectory = $this->getParameter('upload_destination.track');
                    $fitFilePath = sprintf('%s/%s', $trackDirectory, $trackFilename);

                    $gpxContent = $fitConverter->convertToGpxString($fitFilePath);
                    $uploadFaker->fakeUpload($track, 'trackFile', $gpxContent, 'upload.gpx');

                    $em->persist($track);
                } catch (\Exception $e) {
                    return $this->render('Track/upload.html.twig', [
                        'form' => $form->createView(),
                        'ride' => $ride,
                        'errorMessage' => sprintf('FIT-Datei konnte nicht konvertiert werden: %s', $e->getMessage()),
                    ]);
                }
            }

            $track
                ->setRide($ride)
                ->setUser($this->getUser())
                ->setUsername($this->getUser()->getUsername())
                ->setSource($isFitFile ? Track::TRACK_SOURCE_FIT : Track::TRACK_SOURCE_GPX);

            $em->persist($track);
            $em->flush();

            $eventDispatcher->dispatch(new TrackUploadedEvent($track), TrackUploadedEvent::NAME);

            return $this->redirect($objectRouter->generate($track));
        }

        return $this->uploadGetAction($ride, $form);
    }
}
