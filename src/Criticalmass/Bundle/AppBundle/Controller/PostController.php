<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Criticalmass\Bundle\AppBundle\EntityInterface\PostableInterface;
use Criticalmass\Component\Util\ClassUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Post;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\Thread;
use Criticalmass\Bundle\AppBundle\EntityInterface\BoardInterface;
use Criticalmass\Bundle\AppBundle\Form\Type\PostType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function writeAction(
        Request $request,
        int $cityId = null,
        int $rideId = null,
        int $photoId = null,
        int $threadId = null
    ): Response {
        /**
         * @var Ride $ride
         * @var City $city
         * @var Thread $thread
         */
        $post = new Post();
        $ride = null;
        $city = null;
        $thread = null;

        if ($cityId) {
            $city = $this->getCityRepository()->find($cityId);

            $form = $this->createForm(PostType::class, $post, [
                'action' => $this->generateActionUrl($city),
            ]);

            $post->setCity($city);

            $redirectUrl = $this->generateObjectUrl($city);
        } elseif ($rideId) {
            $ride = $this->getRideRepository()->find($rideId);
            $city = $this->getCityRepository()->find($ride->getCity());

            $form = $this->createForm(PostType::class, $post, [
                'action' => $this->generateActionUrl($ride),
            ]);

            $post->setCity($city);
            $post->setRide($ride);

            $redirectUrl = $this->generateObjectUrl($ride);
        } elseif ($photoId) {
            $photo = $this->getPhotoRepository()->find($photoId);

            $form = $this->createForm(PostType::class, $post, [
                'action' => $this->generateActionUrl($photo),
            ]);

            $post->setPhoto($photo);

            $redirectUrl = $this->generateObjectUrl($photo);
        } elseif ($threadId) {
            $thread = $this->getThreadRepository()->find($threadId);

            $form = $this->createForm(PostType::class, $post, [
                'action' => $this->generateActionUrl($thread),
            ]);

            $post->setThread($thread);

            $redirectUrl = $this->generateObjectUrl($thread);
        } else {
            $form = $this->createForm(PostType::class, $post,
                ['action' => $this->generateUrl('caldera_criticalmass_timeline_post_write')]);

            $redirectUrl = $this->generateUrl('caldera_criticalmass_frontpage');
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
            }

            $em->flush();

            /* Using the user’s referer will not work as the user might come from the writefailed page and would be
               redirected there again. */
            return new RedirectResponse($redirectUrl);
        } elseif ($form->isSubmitted()) {
            return $this->render('AppBundle:Post:writefailed.html.twig',
                ['form' => $form->createView(), 'ride' => $ride, 'city' => $city]);
        }

        return $this->render('AppBundle:Post:write.html.twig', ['form' => $form->createView()]);
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
        int $cityId = null,
        int $rideId = null,
        int $photoId = null
    ): Response {
        /* We do not want disabled posts. */
        $criteria = ['enabled' => true];

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

        /* Now fetch all posts with matching criteria. */
        $posts = $this->getPostRepository()->findBy($criteria, ['dateTime' => 'DESC']);

        /* And render our shit. */
        return $this->render('AppBundle:Post:list.html.twig', ['posts' => $posts]);
    }

    protected function generateActionUrl(PostableInterface $parentObject): string
    {
        $lcShortname = ClassUtil::getLowercaseShortname($parentObject);

        $routeName = sprintf('caldera_criticalmass_timeline_post_write_%s', $lcShortname);
        $parameterName = sprintf('%sId', $lcShortname);

        $parameter = [$parameterName => $parentObject->getId()];

        return $this->generateUrl($routeName, $parameter);
    }
}
