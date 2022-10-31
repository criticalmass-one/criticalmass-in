<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Criticalmass\Image\PhotoManipulator\PhotoManipulatorInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Form\Type\PhotoCoordType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class PhotoManagementController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction(UserInterface $user = null): Response
    {
        return $this->render('PhotoManagement/user_list.html.twig', [
            'result' => $this->getPhotoRepository()->findRidesWithPhotoCounterByUser($user),
        ]);
    }

    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    public function ridelistAction(Request $request, PaginatorInterface $paginator, Ride $ride): Response
    {
        $query = $this->getPhotoRepository()->buildQueryPhotosByRide($ride);

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

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="App:Photo", options={"id": "photoId"})
     */
    public function deleteAction(Request $request, Photo $photo, ManagerRegistry $registry): Response
    {
        $this->saveReferer($request);

        $photo->setDeleted(true);

        $registry->getManager()->flush();

        return $this->createRedirectResponseForSavedReferer();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function manageAction(Request $request, PaginatorInterface $paginator, Ride $ride): Response
    {
        $query = $this->getPhotoRepository()->buildQueryPhotosByUserAndRide($this->getUser(), $ride);

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

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="App:Photo", options={"id": "photoId"})
     */
    public function toggleAction(Request $request, Photo $photo, ManagerRegistry $registry): Response
    {
        $this->saveReferer($request);

        $photo->setEnabled(!$photo->getEnabled());

        $registry->getManager()->flush();

        return $this->createRedirectResponseForSavedReferer();
    }

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="App:Photo", options={"id": "photoId"})
     */
    public function featuredPhotoAction(Request $request, Photo $photo, ManagerRegistry $registry): Response
    {
        $this->saveReferer($request);

        $photo->getRide()->setFeaturedPhoto($photo);

        $registry->getManager()->flush();

        return $this->createRedirectResponseForSavedReferer();
    }

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="App:Photo", options={"id": "photoId"})
     */
    public function placeSingleAction(Request $request, Photo $photo, ObjectRouterInterface $objectRouter, ManagerRegistry $registry): Response
    {
        $form = $this->createForm(PhotoCoordType::class, $photo, [
            'action' => $objectRouter->generate($photo, 'caldera_criticalmass_photo_place_single')
        ]);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->placeSinglePostAction($request, $photo, $form, $registry);
        } else {
            return $this->placeSingleGetAction($request, $photo, $form, $registry);
        }
    }

    protected function placeSingleGetAction(Request $request, Photo $photo, FormInterface $form, ManagerRegistry $registry): Response
    {
        $this->saveReferer($request);

        $previousPhoto = $this->getPhotoRepository()->getPreviousPhoto($photo);
        $nextPhoto = $this->getPhotoRepository()->getNextPhoto($photo);

        $track = $this->getTrackRepository()->findByUserAndRide($photo->getRide(), $this->getUser());

        return $this->render('PhotoManagement/place.html.twig', [
            'photo' => $photo,
            'previousPhoto' => $previousPhoto,
            'nextPhoto' => $nextPhoto,
            'track' => $track,
            'form' => $form->createView(),
        ]);
    }

    protected function placeSinglePostAction(Request $request, Photo $photo, FormInterface $form, ManagerRegistry $registry): Response
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $photo = $form->getData();

            $registry->getManager()->flush();
        }

        return $this->createRedirectResponseForSavedReferer();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function relocateAction(Ride $ride): Response
    {
        $photos = $this->getPhotoRepository()->findPhotosByUserAndRide($this->getUser(), $ride);

        $track = $this->getTrackRepository()->findByUserAndRide($ride, $this->getUser());

        return $this->render('PhotoManagement/relocate.html.twig', [
            'ride' => $ride,
            'photos' => $photos,
            'track' => $track,
        ]);
    }

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="App:Photo", options={"id": "photoId"})
     */
    public function rotateAction(Request $request, Photo $photo, PhotoManipulatorInterface $photoManipulator): Response
    {
        $this->saveReferer($request);

        $angle = 90;

        if ($request->query->get('rotate') && $request->query->get('rotate') == 'right') {
            $angle = -90;
        }

        $photoManipulator
            ->open($photo)
            ->rotate($angle)
            ->save();

        return $this->createRedirectResponseForSavedReferer();
    }

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="App:Photo", options={"id": "photoId"})
     */
    public function censorAction(Request $request, UserInterface $user = null, Photo $photo, PhotoManipulatorInterface $photoManipulator): Response
    {
        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->censorPostAction($request, $user, $photo, $photoManipulator);
        }

        return $this->censorGetAction($request, $user, $photo, $photoManipulator);
    }

    public function censorGetAction(Request $request, UserInterface $user = null, Photo $photo, PhotoManipulatorInterface $photoManipulator): Response
    {
        return $this->render('PhotoManagement/censor.html.twig', [
            'photo' => $photo,
        ]);
    }

    public function censorPostAction(Request $request, UserInterface $user = null, Photo $photo, PhotoManipulatorInterface $photoManipulator): Response
    {
        $areaDataList = json_decode($request->getContent());

        if (0 === count($areaDataList)) {
            return new Response(null);
        }

        $newFilename = $photoManipulator
            ->open($photo)
            ->censor($areaDataList, intval($request->query->get('width')))
            ->save();

        return new Response($newFilename);
    }
}
