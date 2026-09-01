<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Criticalmass\Geo\FitService\FitToGpxConverterInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Event\Track\TrackUploadedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use App\Criticalmass\UploadValidator\TrackValidator;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\TrackValidatorException;
use App\Controller\AbstractController;
use App\Entity\Ride;
use App\Entity\Track;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    public function uploadAction(Request $request, EventDispatcherInterface $eventDispatcher, ObjectRouterInterface $objectRouter, Ride $ride, TrackValidator $trackValidator, FitToGpxConverterInterface $fitToGpxConverter): Response
    {
        $track = new Track();

        $form = $this->createFormBuilder($track)
            ->setAction($objectRouter->generate($ride, 'caldera_criticalmass_track_upload'))
            ->add('trackFile', VichFileType::class)
            ->getForm();

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->uploadPostAction($request, $eventDispatcher, $objectRouter, $track, $ride, $form, $trackValidator, $fitToGpxConverter);
        } else {
            return $this->uploadGetAction($request, $eventDispatcher, $objectRouter, $ride, $form, $trackValidator);
        }
    }

    protected function uploadGetAction(Request $request, EventDispatcherInterface $eventDispatcher, ObjectRouterInterface $objectRouter, Ride $ride, FormInterface $form, TrackValidator $trackValidator): Response
    {
        return $this->render('Track/upload.html.twig', [
            'form' => $form->createView(),
            'ride' => $ride,
            'errorMessage' => null,
        ]);
    }

    public function uploadPostAction(Request $request, EventDispatcherInterface $eventDispatcher, ObjectRouterInterface $objectRouter, Track $track, Ride $ride, FormInterface $form, TrackValidator $trackValidator, FitToGpxConverterInterface $fitToGpxConverter): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            /** @var Track $track */
            $track = $form->getData();

            // FIT uploads are normalised to GPX up-front, so the rest of this flow
            // (Vich storage, validation, enrichment) is identical to a GPX upload.
            $source = Track::TRACK_SOURCE_GPX;

            try {
                if ($this->normaliseFitToGpx($track, $fitToGpxConverter)) {
                    $source = Track::TRACK_SOURCE_FIT;
                }
            } catch (\RuntimeException $e) {
                return $this->render('Track/upload.html.twig', [
                    'form' => $form->createView(),
                    'ride' => $ride,
                    'errorMessage' => sprintf('Die FIT-Datei konnte nicht gelesen werden: %s', $e->getMessage()),
                ]);
            }

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

            $track
                ->setRide($ride)
                ->setUser($this->getUser())
                ->setUsername($this->getUser()->getUsername())
                ->setSource($source);

            $em->persist($track);
            $em->flush();

            $eventDispatcher->dispatch(new TrackUploadedEvent($track), TrackUploadedEvent::NAME);

            return $this->redirect($objectRouter->generate($track));
        }

        return $this->uploadGetAction($request, $eventDispatcher, $objectRouter, $ride, $form, $trackValidator);
    }

    /**
     * If the uploaded file is a Garmin FIT file, convert it to GPX up-front and replace the
     * track file, so the remainder of the upload flow treats it exactly like a GPX upload.
     *
     * @throws \RuntimeException if the FIT file cannot be parsed
     */
    private function normaliseFitToGpx(Track $track, FitToGpxConverterInterface $fitToGpxConverter): bool
    {
        $uploadedFile = $track->getTrackFile();

        if (!$uploadedFile instanceof UploadedFile) {
            return false;
        }

        if (strtolower((string) $uploadedFile->getClientOriginalExtension()) !== 'fit') {
            return false;
        }

        $gpxXml = $fitToGpxConverter->convertFileToXmlString($uploadedFile->getPathname());

        $temporaryGpxPath = tempnam(sys_get_temp_dir(), 'fit2gpx');

        if ($temporaryGpxPath === false) {
            throw new \RuntimeException('Could not create a temporary file for FIT conversion.');
        }

        file_put_contents($temporaryGpxPath, $gpxXml);

        $gpxFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME) . '.gpx';

        $track->setTrackFile(new UploadedFile($temporaryGpxPath, $gpxFilename, 'application/gpx+xml', null, true));

        return true;
    }
}
