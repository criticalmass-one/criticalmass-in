<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Board;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Event;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Post;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\PostType;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Thread;
use Caldera\Bundle\CriticalmassModelBundle\EntityInterface\BoardInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    public function writeAction(
        Request $request, 
        $cityId = null, 
        $rideId = null, 
        $photoId = null, 
        $contentId = null,
        $threadId = null,
        $eventId = null
    ) {
        /**
         * @var Post $post
         * @var Ride $ride
         * @var City $city
         * @var Thread $thread
         * @var Event $event
         */
        $post = new Post();
        $ride = null;
        $city = null;
        $thread = null;
        $event = null;

        if ($cityId) {
            $form = $this->createForm(new PostType(), $post, array('action' => $this->generateUrl('caldera_criticalmass_timeline_post_write_city', array('cityId' => $cityId))));
            $city = $this->getCityRepository()->find($cityId);
            $post->setCity($city);

            $redirectUrl = $this->generateUrl('caldera_criticalmass_desktop_city_show', array('citySlug' => $city->getMainSlugString()));
        } elseif ($rideId) {
            $form = $this->createForm(new PostType(), $post, array('action' => $this->generateUrl('caldera_criticalmass_timeline_post_write_ride', array('rideId' => $rideId))));
            $ride = $this->getRideRepository()->find($rideId);
            $city = $this->getCityRepository()->find($ride->getCity());
            $post->setCity($city);
            $post->setRide($ride);

            $redirectUrl = $this->generateUrl('caldera_criticalmass_ride_show', array('citySlug' => $ride->getCity()->getMainSlugString(), 'rideDate' => $ride->getFormattedDate()));
        } elseif ($photoId) {
            $form = $this->createForm(new PostType(), $post, array('action' => $this->generateUrl('caldera_criticalmass_timeline_post_write_photo', array('photoId' => $photoId))));
            $photo = $this->getPhotoRepository()->find($photoId);
            $post->setPhoto($photo);

            $redirectUrl = $this->generateUrl($photo);
        } elseif ($contentId) {
            $form = $this->createForm(new PostType(), $post, array('action' => $this->generateUrl('caldera_criticalmass_timeline_post_write_content', array('contentId' => $contentId))));
            $content = $this->getContentRepository()->find($contentId);
            $post->setContent($content);

            $redirectUrl = $this->generateUrl('caldera_criticalmass_content_display', array('slug' => $content->getSlug()));
        } elseif ($threadId) {
            $form = $this->createForm(new PostType(), $post, array('action' => $this->generateUrl('caldera_criticalmass_timeline_post_write_thread', array('threadId' => $threadId))));

            $thread = $this->getThreadRepository()->find($threadId);
            $post->setThread($thread);

            $redirectUrl = $this->generateUrl($thread);
        } elseif ($eventId) {
            $form = $this->createForm(new PostType(), $post, array('action' => $this->generateUrl('caldera_criticalmass_timeline_post_write_event', array('eventId' => $eventId))));

            $event = $this->getEventRepository()->find($eventId);
            $post->setEvent($event);

            $redirectUrl = $this->generateUrl($event);
        } else {
            $form = $this->createForm(new PostType(), $post, array('action' => $this->generateUrl('caldera_criticalmass_timeline_post_write')));

            $redirectUrl = $this->generateUrl('caldera_criticalmass_timeline_list');
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $post->setUser($this->getUser());
            $em->persist($post);

            /* if we have a thread we need some additional behaviour here after the post is persisted */
            if ($thread) {
                $thread->setLastPost($post);
                $thread->incPostNumber();

                /**
                 * @var BoardInterface $board
                 */
                if ($thread->getBoard()) {
                    $board = $thread->getBoard();
                } else {
                    $board = $thread->getCity();
                }

                $board->incPostNumber();
                $board->setLastThread($thread);

                $em->persist($thread);
                $em->persist($board);
            }

            $em->flush();

            /* Using the user’s referer will not work as the user might come from the writefailed page and would be
               redirected there again. */
            return new RedirectResponse($redirectUrl);
        } elseif ($form->isSubmitted()) {
            return $this->render('CalderaCriticalmassSiteBundle:Post:writefailed.html.twig', array('form' => $form->createView(), 'ride' => $ride, 'city' => $city));
        }

        return $this->render('CalderaCriticalmassSiteBundle:Post:write.html.twig', array('form' => $form->createView()));
    }


    /**
     * List all posts.
     *
     * This action handles different cases:
     *
     * If you provide a $cityId, it will just list posts for this city.
     * If you provide a $rideId, it will list all posts for the specified ride.
     * If you call this method without any parameters, it will list everything in a timeline style.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(
        Request $request, 
        $cityId = null, 
        $rideId = null, 
        $photoId = null, 
        $contentId = null,
        $eventId = null
    ) {
        /* We do not want disabled posts. */
        $criteria = array('enabled' => true);

        /* If a $cityId is provided, add the city to the criteria. */
        if ($cityId) {
            $criteria['city'] = $cityId;
        }

        /* If a $rideId is provided, add the ride to the criteria. */
        if ($rideId) {
            $criteria['ride'] = $rideId;
        }

        if ($photoId) {
            $criteria['photo'] = $photoId;
        }

        if ($contentId) {
            $criteria['content'] = $contentId;
        }

        if ($eventId) {
            $criteria['event'] = $eventId;
        }

        /* Now fetch all posts with matching criteria. */
        $posts = $this->getPostRepository()->findBy($criteria, array('dateTime' => 'DESC'));

        /* And render our shit. */
        return $this->render('CalderaCriticalmassSiteBundle:Post:list.html.twig', array('posts' => $posts));
    }
}
