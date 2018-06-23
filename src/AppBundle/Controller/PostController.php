<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Photo;
use AppBundle\EntityInterface\PostableInterface;
use AppBundle\Repository\PostRepository;
use AppBundle\Criticalmass\Util\ClassUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\City;
use AppBundle\Entity\Post;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Thread;
use AppBundle\EntityInterface\BoardInterface;
use AppBundle\Form\Type\PostType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="AppBundle:City", converter="city_converter")
     */
    public function writeCityAction(Request $request, City $city): Response
    {
        return $this->writeAction($request, $city);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride", converter="ride_converter")
     */
    public function writeRideAction(Request $request, Ride $ride): Response
    {
        return $this->writeAction($request, $ride);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("photo", class="AppBundle:Photo", converter="photo_converter")
     */
    public function writePhotoAction(Request $request, Photo $photo): Response
    {
        return $this->writeAction($request, $photo);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("thread", class="AppBundle:Thread", isOptional=true, converter="thread_converter")
     */
    public function writeThreadAction(Request $request, Thread $thread = null): Response
    {
        return $this->writeAction($request, $thread);
    }

    public function writeAction(Request $request, PostableInterface $postable): Response
    {
        $post = $this->createPostForPostable($postable);

        $form = $this->getPostForm($postable, $post);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->addPostAction($request, $form, $post, $postable);
        } else {
            return $this->addGetAction($request, $form, $post,$postable);
        }
    }

    protected function addGetAction(Request $request, FormInterface $form, Post $post, PostableInterface $postable): Response
    {
        return $this->render('AppBundle:Post:write.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function addPostAction(Request $request, FormInterface $form, Post $post, PostableInterface $postable): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $post->setUser($this->getUser());
            $em->persist($post);

            /* if we have a thread we need some additional behaviour here after the post is persisted */
            if ($postable instanceof Thread) {
                $postable
                    ->setLastPost($post)
                    ->incPostNumber();

                /** @var BoardInterface $board */
                if ($postable->getBoard()) {
                    $board = $postable->getBoard();
                } else {
                    $board = $postable->getCity();
                }

                $board->incPostNumber();
                $board->setLastThread($postable);
            }

            $em->flush();

            return new RedirectResponse($this->generateObjectUrl($postable));
        }

        return $this->render('AppBundle:Post:write_failed.html.twig', [
            'form' => $form->createView(),
        ]);
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

    protected function createPostForPostable(PostableInterface $postable): Post
    {
        $post = new Post();

        $shortname = ClassUtil::getShortname($postable);
        $setMethodName = sprintf('set%s', $shortname);

        $post->$setMethodName($postable);

        return $post;
    }
}
