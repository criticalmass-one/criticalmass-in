<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Photo;
use App\EntityInterface\PostableInterface;
use App\Criticalmass\Util\ClassUtil;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\City;
use App\Entity\Post;
use App\Entity\Ride;
use App\Entity\Thread;
use App\EntityInterface\BoardInterface;
use App\Form\Type\PostType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function writeCityAction(Request $request, City $city, ObjectRouterInterface $objectRouter): Response
    {
        return $this->writeAction($request, $city, $objectRouter);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function writeRideAction(Request $request, Ride $ride, ObjectRouterInterface $objectRouter): Response
    {
        return $this->writeAction($request, $ride, $objectRouter);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function writePhotoAction(Request $request, Photo $photo, ObjectRouterInterface $objectRouter): Response
    {
        return $this->writeAction($request, $photo, $objectRouter);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function writeThreadAction(Request $request, Thread $thread = null, ObjectRouterInterface $objectRouter): Response
    {
        return $this->writeAction($request, $thread, $objectRouter);
    }

    public function writeAction(Request $request, PostableInterface $postable, ObjectRouterInterface $objectRouter): Response
    {
        $post = $this->createPostForPostable($postable);

        $form = $this->getPostForm($postable, $post);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->addPostAction($request, $form, $post, $postable, $objectRouter);
        } else {
            return $this->addGetAction($request, $form, $post, $postable, $objectRouter);
        }
    }

    protected function addGetAction(Request $request, FormInterface $form, Post $post, PostableInterface $postable, ObjectRouterInterface $objectRouter): Response
    {
        return $this->render('Post/write.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function addPostAction(Request $request, FormInterface $form, Post $post, PostableInterface $postable, ObjectRouterInterface $objectRouter): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

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

            return $this->redirect($objectRouter->generate($postable));
        }

        return $this->render('Post/write_failed.html.twig', [
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
        PostRepository $postRepository,
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
        $posts = $postRepository->findBy($criteria, ['dateTime' => 'DESC']);

        /* And render our shit. */
        return $this->render('Post/list.html.twig', ['posts' => $posts]);
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
        $lcfirstShortname = ClassUtil::getLcfirstShortname($postable);

        $routeName = sprintf('caldera_criticalmass_timeline_post_write_%s', $lcShortname);
        $parameterName = sprintf('%sId', $lcfirstShortname);
        $parameter = [$parameterName => $postable->getId()];

        if ($postable instanceof Thread) {
            $parameter = ['threadSlug' => $postable->getSlug()];
        }

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
