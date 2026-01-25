<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Criticalmass\Image\PhotoManipulator\PhotoManipulatorInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Form\Type\PhotoCoordType;
use App\Repository\PhotoRepository;
use App\Repository\TrackRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PhotoManagementController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/photos/list', name: 'caldera_criticalmass_photo_user_list', priority: 180)]
    public function listAction(
        PhotoRepository $photoRepository,
        ?UserInterface $user = null
    ): Response {
        return $this->render('PhotoManagement/user_list.html.twig', [
            'result' => $photoRepository->findRidesWithPhotoCounterByUser($user),
        ]);
    }

    public function ridelistAction(
        Request $request,
        PaginatorInterface $paginator,
        PhotoRepository $photoRepository,
        Ride $ride
    ): Response {
        $query = $photoRepository->buildQueryPhotosByRide($ride);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render('PhotoManagement/ride_list.html.twig', [
            'ride' => $ride,
            'pagination' => $pagination,
        ]);
    }

    #[IsGranted('edit', 'photo')]
    #[Route('/managephotos/{id}/delete', name: 'caldera_criticalmass_photo_delete', priority: 170)]
    public function deleteAction(Request $request, Photo $photo, ManagerRegistry $registry): Response
    {
        $this->saveReferer($request);

        $photo->setDeleted(true);

        $registry->getManager()->flush();

        return $this->createRedirectResponseForSavedReferer($request);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{citySlug}/{rideIdentifier}/managephotos', name: 'caldera_criticalmass_photo_manage', priority: 170)]
    public function manageAction(
        Request $request,
        PaginatorInterface $paginator,
        Ride $ride,
        PhotoRepository $photoRepository
    ): Response {
        $query = $photoRepository->buildQueryPhotosByUserAndRide($this->getUser(), $ride);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render('PhotoManagement/manage.html.twig', [
            'ride' => $ride,
            'pagination' => $pagination
        ]);
    }

    #[IsGranted('edit', 'photo')]
    #[Route('/photo/{id}/toggle', name: 'caldera_criticalmass_photo_toggle', priority: 170)]
    public function toggleAction(Request $request, Photo $photo, ManagerRegistry $registry): Response
    {
        $this->saveReferer($request);

        $photo->setEnabled(!$photo->getEnabled());

        $registry->getManager()->flush();

        return $this->createRedirectResponseForSavedReferer($request);
    }

    #[IsGranted('edit', 'photo')]
    #[Route('/photo/{id}/featured', name: 'caldera_criticalmass_photo_featured', priority: 170)]
    public function featuredPhotoAction(Request $request, Photo $photo, ManagerRegistry $registry): Response
    {
        $this->saveReferer($request);

        $photo->getRide()->setFeaturedPhoto($photo);

        $registry->getManager()->flush();

        return $this->createRedirectResponseForSavedReferer($request);
    }

    #[IsGranted('edit', 'photo')]
    #[Route('/photo/{id}/place', name: 'caldera_criticalmass_photo_place_single', priority: 170)]
    public function placeSingleAction(
        Request $request,
        Photo $photo,
        ObjectRouterInterface $objectRouter,
        ManagerRegistry $registry
    ): Response {
        $form = $this->createForm(PhotoCoordType::class, $photo, [
            'action' => $objectRouter->generate($photo, 'caldera_criticalmass_photo_place_single')
        ]);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->placeSinglePostAction($request, $photo, $form, $registry);
        }

        return $this->placeSingleGetAction($request, $photo, $form, $registry);
    }

    protected function placeSingleGetAction(
        Request $request,
        Photo $photo,
        FormInterface $form,
        TrackRepository $trackRepository
    ): Response {
        $this->saveReferer($request);

        $track = $trackRepository->findByUserAndRide($photo->getRide(), $this->getUser());

        return $this->render('PhotoManagement/place.html.twig', [
            'photo' => $photo,
            'track' => $track,
            'form' => $form->createView(),
        ]);
    }

    protected function placeSinglePostAction(
        Request $request,
        Photo $photo,
        FormInterface $form,
        ManagerRegistry $registry
    ): Response {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $photo = $form->getData();

            $registry->getManager()->flush();
        }

        return $this->createRedirectResponseForSavedReferer($request);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{citySlug}/{rideIdentifier}/relocatephotos', name: 'caldera_criticalmass_photo_relocate', priority: 170)]
    public function relocateAction(
        TrackRepository $trackRepository,
        PhotoRepository $photoRepository,
        Ride $ride
    ): Response {
        $photos = $photoRepository->findPhotosByUserAndRide($this->getUser(), $ride);

        $track = $trackRepository->findByUserAndRide($ride, $this->getUser());

        return $this->render('PhotoManagement/relocate.html.twig', [
            'ride' => $ride,
            'photos' => $photos,
            'track' => $track,
        ]);
    }

    #[IsGranted('edit', 'photo')]
    #[Route('/photo/{id}/rotate', name: 'caldera_criticalmass_photo_rotate', priority: 170)]
    public function rotateAction(Request $request, Photo $photo, PhotoManipulatorInterface $photoManipulator): Response
    {
        $this->saveReferer($request);

        $angle = 90;

        if ($request->query->get('rotate') && $request->query->get('rotate') === 'right') {
            $angle = -90;
        }

        $photoManipulator
            ->open($photo)
            ->rotate($angle)
            ->save();

        return $this->createRedirectResponseForSavedReferer($request);
    }

    #[IsGranted('edit', 'photo')]
    #[Route('/photo/{id}/censor', name: 'caldera_criticalmass_photo_censor', priority: 170)]
    #[Route('/photo/{id}/censor', name: 'caldera_criticalmass_photo_censor_short', options: ['expose' => true], priority: 170)]
    public function censorAction(
        Request $request,
        Photo $photo,
        PhotoManipulatorInterface $photoManipulator,
        ?UserInterface $user = null
    ): Response {
        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->censorPostAction($request, $photo, $photoManipulator, $user);
        }

        return $this->censorGetAction($photo, $photoManipulator, $user);
    }

    public function censorGetAction(
        Photo $photo,
        PhotoManipulatorInterface $photoManipulator,
        ?UserInterface $user = null
    ): Response {
        return $this->render('PhotoManagement/censor.html.twig', [
            'photo' => $photo,
        ]);
    }

    public function censorPostAction(
        Request $request,
        Photo $photo,
        PhotoManipulatorInterface $photoManipulator,
        ?UserInterface $user = null
    ): Response {
        $areaDataList = json_decode($request->getContent());

        if (0 === count($areaDataList)) {
            return new Response(null);
        }

        $newFilename = $photoManipulator
            ->open($photo)
            ->censor($areaDataList, (int) $request->query->get('width'))
            ->save();

        return new Response($newFilename);
    }

    protected function saveReferer(Request $request): string
    {
        $referer = $request->headers->get('referer');

        $request->getSession()->set('referer', $referer);

        return $referer;
    }

    protected function getSavedReferer(Request $request): ?string
    {
        return $request->getSession()->get('referer');
    }

    protected function createRedirectResponseForSavedReferer(Request $request): RedirectResponse
    {
        $referer = $this->getSavedReferer($request);

        if (!$referer) {
            throw new \Exception('No saved referer found to redirect to.');
        }

        return new RedirectResponse($referer);
    }
}
