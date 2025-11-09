<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Track\Export\TrackExporterInterface;
use App\Criticalmass\Track\Upload\TrackUploaderInterface;
use App\Entity\Ride;
use App\Entity\Track;
use App\Form\Type\TrackUploadType;
use App\Repository\TrackRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/track', name: 'criticalmass_track_')]
class TrackController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/upload/{citySlug}/{rideIdentifier}', name: 'upload', priority: 150)]
    public function uploadAction(
        Request $request,
        Ride $ride,
        TrackUploaderInterface $trackUploader,
        ManagerRegistry $registry,
        UserInterface $user = null
    ): Response {
        $track = new Track();
        $track->setRide($ride);
        $track->setUser($user);

        $form = $this->createForm(TrackUploadType::class, $track);
        $form->add('submit', SubmitType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->uploadPostAction($request, $form, $trackUploader, $registry);
        }

        return $this->uploadGetAction($form, $ride);
    }

    protected function uploadGetAction(FormInterface $form, Ride $ride): Response
    {
        return $this->render('Track/upload.html.twig', [
            'ride' => $ride,
            'form' => $form->createView(),
        ]);
    }

    protected function uploadPostAction(
        Request $request,
        FormInterface $form,
        TrackUploaderInterface $trackUploader,
        ManagerRegistry $registry
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Track $track */
            $track = $form->getData();

            $trackUploader->upload($track);
            $registry->getManager()->persist($track);
            $registry->getManager()->flush();

            $this->addFlash('success', 'Deine Strecke wurde erfolgreich hochgeladen!');
            return $this->redirectToRoute('criticalmass_track_view', ['id' => $track->getId()]);
        }

        return $this->render('Track/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'view', requirements: ['id' => '\d+'], priority: 150)]
    public function viewAction(Track $track, TrackRepository $trackRepository): Response
    {
        $previousTrack = $trackRepository->findPreviousTrack($track);
        $nextTrack = $trackRepository->findNextTrack($track);

        return $this->render('Track/view.html.twig', [
            'track' => $track,
            'previousTrack' => $previousTrack,
            'nextTrack' => $nextTrack,
        ]);
    }

    #[Route('/list/{citySlug}/{rideIdentifier}', name: 'list', priority: 150)]
    public function listAction(TrackRepository $trackRepository, Ride $ride): Response
    {
        return $this->render('Track/list.html.twig', [
            'ride' => $ride,
            'tracks' => $trackRepository->findTracksByRide($ride),
        ]);
    }

    #[Route('/export/{id}', name: 'export', priority: 150)]
    public function exportAction(Track $track, TrackExporterInterface $trackExporter): Response
    {
        $content = $trackExporter->export($track);

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/gpx+xml');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="track-%d.gpx"', $track->getId()));

        return $response;
    }

    #[IsGranted('delete', 'track')]
    #[Route('/delete/{id}', name: 'delete', methods: ['POST'], priority: 150)]
    public function deleteAction(Track $track, ManagerRegistry $registry, ObjectRouterInterface $objectRouter): Response
    {
        $ride = $track->getRide();

        $registry->getManager()->remove($track);
        $registry->getManager()->flush();

        $this->addFlash('success', 'Der Track wurde gelöscht.');

        return $this->redirect($objectRouter->generate($ride));
    }
}
