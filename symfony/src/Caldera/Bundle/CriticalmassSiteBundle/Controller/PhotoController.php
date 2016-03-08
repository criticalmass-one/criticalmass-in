<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\PhotoCoordType;
use Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader\DateTimeExifReader;
use Caldera\Bundle\CriticalmassCoreBundle\Image\PhotoGps\PhotoGps;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\PhotoView;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PhotoController extends AbstractController
{
    public function showAction(Request $request, $citySlug, $rideDate = null, $eventSlug = null, $photoId)
    {
        $city = $this->getCheckedCity($citySlug);
        $ride = null;
        $event = null;

        if ($rideDate) {
            $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);
        } else {
            $event = $this->getEventRepository()->findOneBySlug($eventSlug);
        }

        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoRepository()->find($photoId);

        $previousPhoto = $this->getPhotoRepository()->getPreviousPhoto($photo);
        $nextPhoto = $this->getPhotoRepository()->getNextPhoto($photo);

        $this->countView($photo);

        return $this->render('CalderaCriticalmassSiteBundle:Photo:show.html.twig',
            [
                'photo' => $photo,
                'nextPhoto' => $nextPhoto,
                'previousPhoto' => $previousPhoto,
                'city' => $city,
                'ride' => $ride,
                'event' => $event
            ]
        );
    }

    /**
     * Trigger a photo view if the javascript gallery is used.
     *
     * @param Request $request
     * @return Response
     * @author maltehuebner
     * @since 2016
     */
    public function ajaxphotoviewAction(Request $request)
    {
        $photoId = $request->get('photoId');

        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoRepository()->find($photoId);

        if ($photo) {
            $this->countView($photo);
        }

        return new Response(null);
    }

    /**
     * This method saves a call of a photo. This is done by storing the view
     * into the memcache server to avoid unwanted database i/o by hundreds of
     * write operations per minute.
     *
     * In the background there is a cron awaking a symfony command to store all
     * those views in the database. Basically we just increase the memcached
     * number of pending views and add a new array with datetime information
     * about this view.
     *
     * @param Photo $photo
     * @author maltehuebner
     * @since 2016
     */
    protected function countView(Photo $photo)
    {
        $memcache = $this->get('memcache.criticalmass');

        // first get the number of currently memcached views for this photo
        $additionalPhotoViews = $memcache->get('gallery_photo'.$photo->getId().'_additionalviews');

        // are there already any views stored by memcache?
        if (!$additionalPhotoViews) {
            // okay, then we start with view number 1
            $additionalPhotoViews = 1;
        } else {
            // otherwise increase number of photo views
            ++$additionalPhotoViews;
        }

        $viewDateTime = new \DateTime();

        // build an array to be stored as a new PhotoView entity later
        $photoViewArray =
            [
                // photo id
                'photoId' => $photo->getId(),
                // user id
                'userId' => ($this->getUser() ? $this->getUser()->getId() : null),
                // datetime
                'dateTime' => $viewDateTime->format('Y-m-d H:i:s')
            ]
        ;

        // update the number of memcached views
        $memcache->set('gallery_photo'.$photo->getId().'_additionalviews', $additionalPhotoViews);

        // add this view’s data to memcache, too
        $memcache->set('gallery_photo'.$photo->getId().'_view'.$additionalPhotoViews, $photoViewArray);
    }
}
