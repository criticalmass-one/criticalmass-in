<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\EntityInterface\PostableInterface;
use Criticalmass\Component\Util\ClassUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Post;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\Thread;
use Criticalmass\Bundle\AppBundle\EntityInterface\BoardInterface;
use Criticalmass\Bundle\AppBundle\Form\Type\PostType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="AppBundle:City", isOptional=true, converter="city_converter")
     * @ParamConverter("ride", class="AppBundle:Ride", isOptional=true, converter="ride_converter")
     * @ParamConverter("photo", class="AppBundle:Photo", isOptional=true, converter="photo_converter")
     * @ParamConverter("thread", class="AppBundle:Thread", isOptional=true, converter="thread_converter")
     */
    public function writeAction(
        Request $request,
        City $city = null,
        Ride $ride = null,
        Photo $photo = null,
        Thread $thread = null
    ): Response {
        $post = new Post();

        if ($city) {
            $form = $this->getPostForm($city, $post);

            $post->setCity($city);
        } elseif ($ride) {
            $city = $this->getCityRepository()->find($ride->getCity());

            $form = $this->getPostForm($ride, $post);

            $post->setCity($city);
            $post->setRide($ride);
        } elseif ($photo) {
            $form = $this->getPostForm($photo, $post);

            $post->setPhoto($photo);
        } elseif ($thread) {
            $form = $this->getPostForm($thread, $post);

            $post->setThread($thread);
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->addPostAction($request, $form, $post, $city, $ride, $photo, $thread);
        } else {
            return $this->addGetAction($request, $form, $post, $city, $ride, $photo, $thread);
        }
    }

    protected function addGetAction(Request $request, FormInterface $form, Post $post, City $city = null, Ride $ride = null, Photo $photo = null, Thread $thread = null): Response
    {
        return $this->render('AppBundle:Post:write.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function addPostAction(Request $request, FormInterface $form, Post $post, City $city = null, Ride $ride = null, Photo $photo = null, Thread $thread = null): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            return new RedirectResponse($this->generateObjectUrl($thread ?? $photo ?? $ride ?? $city));
        }

        return $this->addGetAction($request, $form, $post, $city, $ride, $photo, $thread);
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

    protected function getPostForm(PostableInterface $postable, Post $post = null): FormInterface
    {
        if (!$post) {
            $post = new Post();
        }

        $form = $this->createForm(PostType::class, $post, [
            'action' => $this->generateActionUrl($postable),
        ]);

        return $form;
    }

    protected function generateActionUrl(PostableInterface $postable): string
    {
        $lcShortname = ClassUtil::getLowercaseShortname($postable);

        $routeName = sprintf('caldera_criticalmass_timeline_post_write_%s', $lcShortname);
        $parameterName = sprintf('%sId', $lcShortname);

        $parameter = [$parameterName => $postable->getId()];

        return $this->generateUrl($routeName, $parameter);
    }
}
