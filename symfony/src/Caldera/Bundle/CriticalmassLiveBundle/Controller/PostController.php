<?php

namespace Caldera\Bundle\CriticalmassLiveBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\BlogPost;
use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Event;
use Caldera\Bundle\CalderaBundle\Entity\Post;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Thread;
use Caldera\Bundle\CalderaBundle\EntityInterface\BoardInterface;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\PostType;
use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    public function writeAction(
        Request $request,
        $cityId = null,
        $rideId = null
    ) {
        /**
         * @var Post $post
         * @var Ride $ride
         * @var City $city
         * @var Thread $thread
         * @var Event $event
         * @var BlogPost $blogPost
         */
        $ride = null;

        $form = null;

        if ($cityId) {
            $form = $this->createForm(PostType::class, $post, array('action' => $this->generateUrl('caldera_criticalmass_timeline_post_write_city', array('cityId' => $cityId))));
            $city = $this->getCityRepository()->find($cityId);
            $post->setCity($city);

            $redirectUrl = $this->generateUrl('caldera_criticalmass_desktop_city_show', array('citySlug' => $city->getMainSlugString()));
        } elseif ($rideId) {
            $form = $this->createForm(PostType::class, $post, array('action' => $this->generateUrl('caldera_criticalmass_timeline_post_write_ride', array('rideId' => $rideId))));
            $ride = $this->getRideRepository()->find($rideId);
            $city = $this->getCityRepository()->find($ride->getCity());
            $post->setCity($city);
            $post->setRide($ride);

            $redirectUrl = $this->generateUrl('caldera_criticalmass_ride_show', array('citySlug' => $ride->getCity()->getMainSlugString(), 'rideDate' => $ride->getFormattedDate()));
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
        $eventId = null,
        $blogPostId = null
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

        if ($blogPostId) {
            $criteria['blogPost'] = $blogPostId;
        }

        /* Now fetch all posts with matching criteria. */
        $posts = $this->getPostRepository()->findBy($criteria, array('dateTime' => 'DESC'));

        /* And render our shit. */
        return $this->render('CalderaCriticalmassSiteBundle:Post:list.html.twig', array('posts' => $posts));
    }
}
