<?php

namespace AppBundle\Controller\Photo;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use AppBundle\Form\Type\PhotoCoordType;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Imagine\Imagick\Imagine;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PhotoManagementController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction(UserInterface $user): Response
    {
        $this->errorIfFeatureDisabled('photos');

        return $this->render('AppBundle:PhotoManagement:user_list.html.twig', [
            'result' => $this->getPhotoRepository()->findRidesWithPhotoCounterByUser($user),
        ]);
    }

    /**
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function ridelistAction(Request $request, Paginator $paginator, Ride $ride): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $query = $this->getPhotoRepository()->buildQueryPhotosByRide($ride);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render('AppBundle:PhotoManagement:ride_list.html.twig', [
            'ride' => $ride,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="AppBundle:Photo", options={"id": "photoId"})
     */
    public function deleteAction(Request $request, Photo $photo): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $this->saveReferer($request);

        $photo->setDeleted(true);

        $this->getManager()->flush();

        return $this->createRedirectResponseForSavedReferer();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function manageAction(Request $request, Paginator $paginator, Ride $ride): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $query = $this->getPhotoRepository()->buildQueryPhotosByUserAndRide($this->getUser(), $ride);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render('AppBundle:PhotoManagement:manage.html.twig', [
            'ride' => $ride,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="AppBundle:Photo", options={"id": "photoId"})
     */
    public function toggleAction(Request $request, Photo $photo): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $this->saveReferer($request);

        $photo->setEnabled(!$photo->getEnabled());

        $this->getManager()->flush();

        return $this->createRedirectResponseForSavedReferer();
    }

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="AppBundle:Photo", options={"id": "photoId"})
     */
    public function featuredPhotoAction(Request $request, Photo $photo): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $this->saveReferer($request);

        $photo->getRide()->setFeaturedPhoto($photo);

        $this->getManager()->flush();

        return $this->createRedirectResponseForSavedReferer();
    }

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="AppBundle:Photo", options={"id": "photoId"})
     */
    public function placeSingleAction(Request $request, Photo $photo): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $form = $this->createForm(PhotoCoordType::class, $photo, [
            'action' => $this->generateUrl('caldera_criticalmass_photo_place_single', [
                'photoId' => $photo->getId(),
            ])
        ]);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->placeSinglePostAction($request, $photo, $form);
        } else {
            return $this->placeSingleGetAction($request, $photo, $form);
        }
    }

    protected function placeSingleGetAction(Request $request, Photo $photo, Form $form): Response
    {
        $this->saveReferer($request);

        $previousPhoto = $this->getPhotoRepository()->getPreviousPhoto($photo);
        $nextPhoto = $this->getPhotoRepository()->getNextPhoto($photo);

        $track = $this->getTrackRepository()->findByUserAndRide($photo->getRide(), $this->getUser());

        return $this->render('AppBundle:PhotoManagement:place.html.twig', [
            'photo' => $photo,
            'previousPhoto' => $previousPhoto,
            'nextPhoto' => $nextPhoto,
            'track' => $track,
            'form' => $form->createView(),
        ]);
    }

    protected function placeSinglePostAction(Request $request, Photo $photo, Form $form): Response
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $photo = $form->getData();

            $this->getManager()->flush();
        }

        return $this->createRedirectResponseForSavedReferer();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function relocateAction(Ride $ride): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $photos = $this->getPhotoRepository()->findPhotosByUserAndRide($this->getUser(), $ride);

        $track = $this->getTrackRepository()->findByUserAndRide($ride, $this->getUser());

        return $this->render('AppBundle:PhotoManagement:relocate.html.twig', [
            'ride' => $ride,
            'photos' => $photos,
            'track' => $track,
        ]);
    }

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="AppBundle:Photo", options={"id": "photoId"})
     */
    public function rotateAction(Request $request, Photo $photo): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $this->saveReferer($request);

        $angle = 90;

        if ($request->query->get('rotate') && $request->query->get('rotate') == 'right') {
            $angle = -90;
        }

        $imagine = new Imagine();

        $image = $imagine->open($this->getPhotoFilename($photo));

        $image->rotate($angle);

        $this->saveManipulatedImage($image, $photo);

        return $this->createRedirectResponseForSavedReferer();
    }

    /**
     * @Security("is_granted('edit', photo)")
     * @ParamConverter("photo", class="AppBundle:Photo", options={"id": "photoId"})
     */
    public function censorAction(Request $request, UserInterface $user, Photo $photo): Response
    {
        $this->errorIfFeatureDisabled('photos');

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->censorPostAction($request, $user, $photo);
        } else {
            return $this->censorGetAction($request, $user, $photo);
        }
    }

    public function censorGetAction(Request $request, UserInterface $user, Photo $photo): Response
    {
        return $this->render('AppBundle:PhotoManagement:censor.html.twig', [
            'photo' => $photo,
        ]);
    }

    public function censorPostAction(Request $request, UserInterface $user, Photo $photo): Response
    {
        $areaDataList = json_decode($request->getContent());

        if (0 === count($areaDataList)) {
            return new Response(null);
        }

        $displayWidth = $request->query->get('width');

        $imagine = new Imagine();

        $image = $imagine->open($this->getPhotoFilename($photo));

        $size = $image->getSize();

        $factor = $size->getWidth() / $displayWidth;

        foreach ($areaDataList as $areaData) {
            $topLeftPoint = new Point($areaData->x * $factor, $areaData->y * $factor);
            $dimension = new Box($areaData->width * $factor, $areaData->height * $factor);

            $this->applyBlurArea($image, $topLeftPoint, $dimension);
        }

        $newFilename = $this->saveManipulatedImage($image, $photo);

        return new Response($newFilename);
    }

    protected function applyBlurArea(ImageInterface $image, PointInterface $topLeftPoint, BoxInterface $dimension): void
    {
        $blurImage = $image->copy();

        $pixelateDimension = $dimension->scale(0.01);

        $blurImage
            ->crop($topLeftPoint, $dimension)
            ->resize($pixelateDimension, ImageInterface::FILTER_CUBIC)
            ->resize($dimension, ImageInterface::FILTER_CUBIC);

        $image->paste($blurImage, $topLeftPoint);
    }

    protected function getPhotoFilename(Photo $photo): string
    {
        $path = $this->getParameter('kernel.root_dir') . '/../web';
        $filename = $this->get('vich_uploader.templating.helper.uploader_helper')->asset($photo, 'imageFile');

        return $path . $filename;
    }

    protected function saveManipulatedImage(ImageInterface $image, Photo $photo): string
    {
        if (!$photo->getBackupName()) {
            $newFilename = uniqid() . '.JPG';

            $photo->setBackupName($photo->getImageName());

            $photo->setImageName($newFilename);

            $this->getDoctrine()->getManager()->flush();
        }

        $filename = $this->getPhotoFilename($photo);
        $image->save($filename);

        $this->recachePhoto($photo);

        return $filename;
    }

    protected function recachePhoto(Photo $photo): void
    {
        $filename = $this->get('vich_uploader.templating.helper.uploader_helper')->asset($photo, 'imageFile');

        $imagineCache = $this->get('liip_imagine.cache.manager');
        $imagineCache->remove($filename);

        $imagineController = $this->get('liip_imagine.controller');
        $imagineController->filterAction(new Request(), $filename, 'gallery_photo_thumb');
        $imagineController->filterAction(new Request(), $filename, 'gallery_photo_standard');
        $imagineController->filterAction(new Request(), $filename, 'gallery_photo_large');
        $imagineController->filterAction(new Request(), $filename, 'city_image_wide');
    }
}
